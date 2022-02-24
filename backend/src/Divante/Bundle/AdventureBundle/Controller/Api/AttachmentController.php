<?php
/**
 * @package  Divante\Bundle
 * @author BH <bh@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Entity\AgreementAttachment;
use Divante\Bundle\AdventureBundle\Message\Agreement\DeleteAttachment;
use Exception;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Divante\Bundle\AdventureBundle\Services\UploadReceiver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Divante\Bundle\AdventureBundle\Entity\AgreementAttachmentTemporaryToken;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class AttachmentController
 * @Route("api/attachment")
 */
class AttachmentController extends FOSRestController
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public const DIRECTORY_IN_UPLOADS = 'documents';


    /**
     * @Route("/upload", name="agreement_attachment_upload")
     * @Method("POST")
     *
     * Access: ADMIN
     *
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     *
     * @param Request $request
     * @param UploadReceiver $receiver
     *
     * @return View
     */
    public function uploadAction(Request $request, UploadReceiver $receiver)
    {
        $repo = $this->getDoctrine()->getRepository(AgreementAttachment::class);
        try {
            $uploadPath = $receiver->getUploadedFilePath(self::DIRECTORY_IN_UPLOADS);
            $entityManager = $this->getDoctrine()->getManager();

            $fileName = $request->get('filename', basename($uploadPath));
            $normalized = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $fileName);
            if (!is_null($repo->findOneBy(['name' => $normalized]))) {
                return $this->view(['error' => 'File already exist'], Response::HTTP_BAD_REQUEST);
            }

            $attachment = new AgreementAttachment();
            $attachment
                ->setPath(basename($uploadPath))
                ->setName($normalized);
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
     * @Route("", name="agreement_attachment_get")
     * @Method("GET")
     *
     * Access: ADMIN
     *
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     *
     * @return View
     */
    public function getAttachments()
    {
        $em = $this->getDoctrine()->getManager();
        $attachments = $em->getRepository(AgreementAttachment::class)->findAll();

        if ($attachments) {
            return $this->view($attachments, Response::HTTP_OK);
        }

        return $this->view([], Response::HTTP_OK);
    }

    /**
     * Delete attachment
     *
     * @Route("/{id}", name="attachment_delete")
     * @Method("DELETE")
     *
     * Access: ADMIN
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     *
     * @param int $id
     *
     * @return View
     */
    public function deleteAction(int $id): View
    {
        $message = new DeleteAttachment($id);
        try {
            $this->messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}/token", name="attachment_prepare_token")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @param AgreementAttachment $attachment
     * @return View
     */
    public function prepareDownloadToken(AgreementAttachment $attachment) : View
    {
        $em = $this->getDoctrine()->getManager();
        $temporaryToken = new AgreementAttachmentTemporaryToken($attachment);
        $em->persist($temporaryToken);
        $em->flush();
        return $this->view(['token' => $temporaryToken->getToken()]);
    }
}
