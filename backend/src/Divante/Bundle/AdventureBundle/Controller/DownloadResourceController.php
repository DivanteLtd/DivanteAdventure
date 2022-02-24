<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 18.03.19
 * Time: 13:42
 */

namespace Divante\Bundle\AdventureBundle\Controller;

use Divante\Bundle\AdventureBundle\Controller\Api\SickLeaveAttachmentController;
use Divante\Bundle\AdventureBundle\Controller\Api\AttachmentController;
use Divante\Bundle\AdventureBundle\Documents\Pdf\HardwareAgreementPdfDocument;
use Divante\Bundle\AdventureBundle\Entity\AgreementAttachmentTemporaryToken;
use Divante\Bundle\AdventureBundle\Entity\AttachmentTemporaryToken;
use Divante\Bundle\AdventureBundle\Entity\Hardware\HardwareAgreement;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DownloadResourceController
 * @package Divante\Bundle\AdventureBundle\Controller
 * @Route("download")
 */
class DownloadResourceController extends Controller
{
    private const TOKEN_TTL = 120; //2 minutes

    /**
     * @Route("/sickleaveattachment", name="download_sickleave_attachment")
     * @Method("GET")
     * @param Request $request
     * @return Response
     */
    public function sickLeaveDownloadResponse(Request $request) : Response
    {
        $token = $request->get('token');
        $em = $this->getDoctrine()->getManager();
        /** @var null|AttachmentTemporaryToken $tokenObj */
        $tokenObj = $em->getRepository(AttachmentTemporaryToken::class)->findOneBy([
            'token' => $token
        ]);
        if (is_null($tokenObj)) {
            return $this->json(['error' => 'token not found'], Response::HTTP_NOT_FOUND);
        }
        if (!$this->isTimestampCorrect($tokenObj)) {
            return $this->json(['error' => 'token has elapsed it\'s usage time'], Response::HTTP_BAD_REQUEST);
        }
        $attachment = $tokenObj->getAttachment();
        $em->remove($tokenObj);
        $em->flush();

        $uploadDir = realpath($this->getParameter('upload_dir'));
        $docRootDir = $uploadDir.'/'.SickLeaveAttachmentController::DIRECTORY_IN_UPLOADS;
        $resource = sprintf('%s/%s', $docRootDir, $attachment->getPath());
        return $this->createDownloadResponse($resource, $attachment->getName());
    }

    /**
     * @Route("/agreementattachment", name="download_agreement_attachment")
     * @Method("GET")
     * @param Request $request
     * @return Response
     */
    public function agreementDownloadResponse(Request $request) : Response
    {
        $token = $request->get('token');
        $em = $this->getDoctrine()->getManager();
        /** @var null|AgreementAttachmentTemporaryToken $tokenObj */
        $tokenObj = $em->getRepository(AgreementAttachmentTemporaryToken::class)->findOneBy([
            'token' => $token
        ]);
        if (is_null($tokenObj)) {
            return $this->json(['error' => 'token not found'], Response::HTTP_NOT_FOUND);
        }
        if (!$this->isTimestampAgreementCorrect($tokenObj)) {
            return $this->json(['error' => 'token has elapsed it\'s usage time'], Response::HTTP_BAD_REQUEST);
        }
        $attachment = $tokenObj->getAttachment();
        $em->remove($tokenObj);
        $em->flush();

        $docRootDir = realpath($this->getParameter('upload_dir')).'/'.AttachmentController::DIRECTORY_IN_UPLOADS;
        $resource = sprintf('%s/%s', $docRootDir, $attachment->getPath());
        return $this->createDownloadResponse($resource, $attachment->getName());
    }


    /**
     * @Route("/report/{token}", name="download_report_attachment")
     * @Method("GET")
     * @param string $token
     * @return Response
     */
    public function reportDownloadResponse(string $token)
    {
        $tmpPath = sprintf($this->get('kernel')->getProjectDir() . '/var/tmp/reports/%s.pdf', $token);
        return $this->createDownloadResponse($tmpPath, 'report.pdf');
    }
    /**
     * @Route("/employee/list/{token}", name="download_person_list_attachment")
     * @Method("GET")
     * @param string $token
     * @return Response
     */
    public function personListDownloadResponse(string $token): Response
    {
        $tmpPath = sprintf($this->get('kernel')->getProjectDir() . '/var/lists/%s.csv', $token);
        return $this->createDownloadResponse($tmpPath, 'employees_list.csv');
    }
    /**
     * @Route("/planner/report/{token}", name="download_planner_report_attachment")
     * @Method("GET")
     * @param string $token
     * @return Response
     */
    public function plannerReportDownloadResponse(string $token)
    {
        $tmpPath = sprintf($this->get('kernel')->getProjectDir() . '/var/tmp/reports/%s', $token);
        return $this->createDownloadResponse($tmpPath, 'planner_report.xlsx');
    }
    private function createDownloadResponse(string $resourcePath, string $destinationFileName) : Response
    {
        $response = new Response(file_get_contents($resourcePath));
        $filename = basename($resourcePath);

        if (FileinfoMimeTypeGuesser::isSupported()) {
            $mimeTypeGuesser = new FileinfoMimeTypeGuesser();
            $mimeType = $mimeTypeGuesser->guess($resourcePath);

            // Mime type of .docx is not recognized very well, sometimes it is an application/zip
            if (false !== strpos($mimeType, 'zip')) {
                $mimeType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            }

            $response->headers->set('Content-Type', $mimeType);
        } else {
            $response->headers->set('Content-Type', 'text/plain');
        }

        $response->headers->set('x-filename', $filename);
        $response->headers->set('Access-Control-Expose-Headers', 'x-filename');
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $destinationFileName,
            ' '
        );
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

    private function isTimestampCorrect(AttachmentTemporaryToken $token) : bool
    {
        $tokenCreation = $token->getCreatedAt()->getTimestamp();
        $tokenDeath = $tokenCreation +self::TOKEN_TTL;
        $currentTimestamp = time();
        return $tokenDeath > $currentTimestamp;
    }

    private function isTimestampAgreementCorrect(AgreementAttachmentTemporaryToken $token) : bool
    {
        $tokenCreation = $token->getCreatedAt()->getTimestamp();
        $tokenDeath = $tokenCreation +self::TOKEN_TTL;
        $currentTimestamp = time();
        return $tokenDeath > $currentTimestamp;
    }

    /**
     * @Route("/hardware-agreement/{id}/{language}/{password}", name="download_hardware_agreement")
     * @Method("GET")
     * @param int $id
     * @param string $language
     * @param string $password
     * @param HardwareAgreementPdfDocument $document
     * @return Response
     * @throws \Exception
     */
    public function downloadHardwareAgreement(
        int $id,
        string $language,
        string $password,
        HardwareAgreementPdfDocument $document
    ) : Response {
        $repo = $this->getDoctrine()->getRepository(HardwareAgreement::class);
        /** @var HardwareAgreement|null $agreement */
        $agreement = $repo->find($id);
        if (is_null($agreement)) {
            throw new NotFoundHttpException("Agreement with ID $id not found");
        }
        if (!password_verify($password, $agreement->getPasswordHashed())) {
            throw new AccessDeniedHttpException("Incorrect password for agreement $id");
        }
        $renderedView = $document->buildPdf($agreement, $password, $language);
        return new Response($renderedView, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Access-Control-Expose-Headers' => 'x-filename',
            'x-filename' => 'agreement.pdf',
        ]);
    }
}
