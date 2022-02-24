<?php
/**
 * Created by PhpStorm.
 * User: LB
 */

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Entity\AttachmentTemporaryToken;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\SickLeaveAttachment;
use Divante\Bundle\AdventureBundle\Services\UploadReceiver;
use Divante\Bundle\AdventureBundle\Entity\User;
use Exception;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class SickLeaveAttachmentController
 *
 * @Route("api/sickleaveattachment")
 */
class SickLeaveAttachmentController extends FOSRestController
{
    protected static int $staticLimit = 10;

    public const DIRECTORY_IN_UPLOADS = 'sick_leaves';

    /**
     * Upload attachment
     *
     * @Route("/upload", name="sickleaveattachment_upload")
     * @Method("POST")
     *
     * Access: SUPER_ADMIN, ADMIN, USER-owner
     *
     * @param Request $request
     *
     * @param UploadReceiver $receiver
     * @return View
     */
    public function uploadAction(Request $request, UploadReceiver $receiver): View
    {
        try {
            $uploadPath = $receiver->getUploadedFilePath(self::DIRECTORY_IN_UPLOADS);
            $entityManager = $this->getDoctrine()->getManager();
            $employeeRepo = $entityManager->getRepository(Employee::class);
            /** @var User $user */
            $user = $this->getUser();
            /** @var Employee|null $employee */
            $employee = $employeeRepo->find($user->getEmployeeId());
            if (is_null($employee)) {
                return $this->view(
                    ['error' => "User with ID {$user->getEmployeeId()} not found"],
                    Response::HTTP_UNAUTHORIZED
                );
            }

            $attachment = new SickLeaveAttachment();
            $attachment
                ->setPath(basename($uploadPath))
                ->setName($request->get('filename', basename($uploadPath)))
                ->setEmployee($employee);
            $entityManager->persist($attachment);
            $entityManager->flush();
            return $this->view([
                'id' => $attachment->getId(),
                'name' => $attachment->getName(),
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            $errorCode = $e->getCode();
            if (!in_array($errorCode, [Response::HTTP_INTERNAL_SERVER_ERROR, Response::HTTP_BAD_REQUEST])) {
                $errorCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            }
            return $this->view(['error' => $e->getMessage()], $errorCode);
        }
    }

    /**
     * @Route("/{id}/token", name="sickleaveattachment_prepare_token")
     * @Method("GET")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     * @param SickLeaveAttachment $attachment
     * @return View
     */
    public function prepareDownloadToken(SickLeaveAttachment $attachment) : View
    {
        $em = $this->getDoctrine()->getManager();
        $temporaryToken = new AttachmentTemporaryToken($attachment);
        $em->persist($temporaryToken);
        $em->flush();
        return $this->view(['token' => $temporaryToken->getToken()]);
    }
}
