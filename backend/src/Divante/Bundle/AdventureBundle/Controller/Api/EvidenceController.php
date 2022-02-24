<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 21.01.19
 * Time: 10:52
 */

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Documents\Pdf\OvertimePdfDocument;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Evidence;
use Divante\Bundle\AdventureBundle\Entity\EvidenceInvoice;
use Divante\Bundle\AdventureBundle\Entity\Notification;
use Divante\Bundle\AdventureBundle\Entity\Repository\EmployeeRepository;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\EmailConfig;
use Divante\Bundle\AdventureBundle\Mappers\EvidenceMapper;
use Divante\Bundle\AdventureBundle\Message\Dashboard\CreateNotification;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailSenderInterface;
use Divante\Bundle\AdventureBundle\Services\EvidenceMessageFactory;
use Divante\Bundle\AdventureBundle\Services\UploadReceiver;
use Divante\Bundle\AdventureBundle\Entity\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class EvidenceController
 * @package Divante\Bundle\AdventureBundle\Controller\Api
 * @Route("/api/evidence")
 */
class EvidenceController extends FOSRestController
{
    public const EVIDENCE_INVOICE_DIRECTORY = "evidence_invoices";

    private EvidenceMapper $evidenceMapper;
    private MessageBusInterface $messageBus;
    protected TranslatorInterface $translator;

    public function __construct(
        EvidenceMapper $evidenceMapper,
        MessageBusInterface $messageBus,
        TranslatorInterface $translator
    ) {
        $this->evidenceMapper = $evidenceMapper;
        $this->messageBus = $messageBus;
        $this->translator = $translator;
    }

    /**
     * @Route("/generate", name="evidence_generate") @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param EvidenceMessageFactory $messageFactory
     * @return View
     * @throws \Exception
     */
    public function generateEvidenceReport(Request $request, EvidenceMessageFactory $messageFactory) : View
    {
        try {
            /** @var User $user */
            $user = $this->getUser();
            $message = $messageFactory->getMessage($user, $request);
            $this->messageBus->dispatch($message);
            if ($message->shouldSendNotification()) {
                $manager = $request->get('manager');
                $path = Notification::REQUESTS_PATH;
                $description = Notification::MANAGER_NEW_EVIDENCE;
                $subject = $user->getEmployee()->getName().' '.$user->getEmployee()->getLastName();
                $createEntry = new CreateNotification($manager, $description, $subject, $path);
                $this->messageBus->dispatch($createEntry);
            }
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        return $this->view(['response' => $message->getSuccessfulResultMessage()]);
    }

    /**
     * @Route("", name="evidence_get")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @return View
     */
    public function getEvidences() : View
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var EmployeeRepository $employeeRepo */
        $employeeRepo = $this->getDoctrine()->getRepository(Employee::class);
        /** @var Employee $employee */
        $employee = $employeeRepo->find($user->getEmployeeId());
        $result = [];
        /** @var Evidence $evidence */
        foreach ($employee->getEmployeeEvidences() as $evidence) {
            $result[] = $this->evidenceMapper->mapEntity($evidence);
        }
        return $this->view($result, Response::HTTP_OK);
    }

    /**
     * @Route("/user/{userId}", name="evidence_get_by_uid")
     * @Method("GET")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param int $userId
     * @return View
     */
    public function getUserEvidences(int $userId) : View
    {
        $employeeRepo = $this->getDoctrine()->getRepository(Employee::class);
        /** @var Employee|null $employee */
        $employee = $employeeRepo->find($userId);
        if (is_null($employee)) {
            return $this->view(['error' => "Employee with ID $userId not found"], Response::HTTP_NOT_FOUND);
        }
        $result = [];
        foreach ($employee->getEmployeeEvidences() as $evidence) {
            $result[] = $this->evidenceMapper->mapEntity($evidence);
        }
        return $this->view($result, Response::HTTP_OK);
    }

    /**
     * @Route("/requests", name="evidence_get_requests")
     * @Method("GET")
     * @Security("has_role('ROLE_MANAGER')")
     * @param EntityManagerInterface $em
     * @return View
     */
    public function getEvidenceRequests(EntityManagerInterface $em) : View
    {
        $user = $this->getUser();
        /** @var EmployeeRepository $employeeRepo */
        $employeeRepo = $em->getRepository(Employee::class);
        /** @var Employee $employee */
        $employee = $employeeRepo->find($user->getEmployeeId());

        $evidences = $em->getRepository(Evidence::class)->findBy([
            'overtimeManager' => $employee
        ], [
            'id' => Criteria::DESC
        ]);

        $response = [];
        /** @var Evidence $evidence */
        foreach ($evidences as $evidence) {
            $response[] = $this->evidenceMapper->mapEntity($evidence);
        }

        return $this->view($response, Response::HTTP_OK);
    }

    /**
     * @Route("/{evidenceId}", name="evidence_update_requests", requirements={"evidenceId"="\d+"}) @Method("POST")
     * @Security("has_role('ROLE_MANAGER')")
     * @param Request $request
     * @param int $evidenceId
     * @param EntityManagerInterface $em
     * @param OvertimePdfDocument $pdf
     * @param EmailSenderInterface $message
     * @param EmailConfig $emailConfig
     * @return View
     * @throws \Exception
     */
    public function saveEvidence(
        Request $request,
        int $evidenceId,
        EntityManagerInterface $em,
        OvertimePdfDocument $pdf,
        EmailSenderInterface $message,
        EmailConfig $emailConfig
    ) : View {
        /** @var Evidence $evidence */
        $evidence = $em->getRepository(Evidence::class)->find($evidenceId);
        $oldStatus = $evidence->getOvertimeStatus();
        $newStatus = $request->get('status', $oldStatus);
        $evidence
            ->setUpdatedAt()
            ->setOvertimeStatus($newStatus);
        $em->flush();

        $employeeLanguage = $evidence->getEmployee()->getLanguage();
        if ($oldStatus !== $newStatus && $newStatus === Evidence::STATUS_OVERTIME_SENT) {
            $pdf->setEvidence($evidence);
            $this->translator->setLocale($employeeLanguage);
            $pdf->buildAndSendPdf([$evidence->getEmployee()->getEmail(), $emailConfig->getEvidenceEmail()]);
        } elseif ($oldStatus !== $newStatus && $newStatus === Evidence::STATUS_OVERTIME_NOT_APPROVED) {
            $this->translator->setLocale($employeeLanguage);
            $subject = $this->translator->trans('yourRequestForAdditionalHoursHasBeenRejected');
            $message->sendMessage(
                $evidence->getEmployee()->getEmail(),
                null,
                sprintf('[Adventure] %s', $subject),
                [
                    'evidence' => $evidence,
                    'language' => $employeeLanguage
                ],
                'AdventureBundle:Emails:evidence_overtime_mail_not_approved.html.twig'
            );
        }
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("/invoice", name="evidence_invoice_upload")
     * @Method("POST")
     * @param Request $request
     * @param UploadReceiver $uploadReceiver
     * @return View
     */
    public function uploadInvoice(Request $request, UploadReceiver $uploadReceiver) : View
    {
        try {
            $uploadPath = $uploadReceiver->getUploadedFilePath(self::EVIDENCE_INVOICE_DIRECTORY);
            $entityManager = $this->getDoctrine()->getManager();
            /** @var User $user */
            $user = $this->getUser();
            $attachment = new EvidenceInvoice();
            $attachment
                ->setOwner($user->getEmployee())
                ->setPath(basename($uploadPath))
                ->setName($request->get('filename', basename($uploadPath)))
                ->setCreatedAt()
                ->setUpdatedAt();
            $entityManager->persist($attachment);
            $entityManager->flush();
            return $this->view([
                'id' => $attachment->getId(),
                'name' => $attachment->getName()
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            $errorCode = $e->getCode();
            if (!in_array($errorCode, [Response::HTTP_INTERNAL_SERVER_ERROR, Response::HTTP_BAD_REQUEST])) {
                $errorCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            }
            return $this->view(['error' => $e->getMessage()], $errorCode);
        }
    }
}
