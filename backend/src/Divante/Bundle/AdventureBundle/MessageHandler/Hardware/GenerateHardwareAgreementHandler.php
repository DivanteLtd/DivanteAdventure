<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Hardware;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Hardware\HardwareAgreement;
use Divante\Bundle\AdventureBundle\Events\Hardware\HardwareSignRequestEvent;
use Divante\Bundle\AdventureBundle\Message\Hardware\GenerateHardwareAgreement;
use Divante\Bundle\AdventureBundle\Services\Hardware\Cipher;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\EmailConfig;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

class GenerateHardwareAgreementHandler
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $eventDispatcher;
    private Cipher $cipher;
    private EmailConfig $emailConfig;
    private TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $em,
        Cipher $cipher,
        EmailConfig $emailConfig,
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator
    ) {
        $this->em = $em;
        $this->cipher = $cipher;
        $this->eventDispatcher = $eventDispatcher;
        $this->emailConfig = $emailConfig;
        $this->translator = $translator;
    }

    public function __invoke(GenerateHardwareAgreement $message) : void
    {
        $id = $message->getId();
        $password = $message->getPassword();
        $agreement = $this->getAgreement($id);
        $agreement
            ->setName($message->getName())
            ->setLastName($message->getLastName())
            ->setNip($this->ciphered($message->getNIP(), $password, $id))
            ->setPesel($this->ciphered($message->getPESEL(), $password, $id))
            ->setCompany($this->ciphered($message->getCompany(), $password, $id))
            ->setHeadquarters($this->ciphered($message->getHeadquarters(), $password, $id))
            ->setGenerationDate(new DateTime())
            ->setPasswordHashed(password_hash($password, PASSWORD_DEFAULT))
            ->setUseLanguages($message->getLanguages());
        $this->em->flush();
        $this->dispatchEvents($agreement, $password);
    }

    private function dispatchEvents(HardwareAgreement $agreement, string $password) : void
    {
        $email = $this->emailConfig->getHelpdeskResponsibleEmail();
        $this->translator->setLocale('en');
        $this->eventDispatcher->dispatch(new HardwareSignRequestEvent($agreement, $password, $email));
    }

    private function ciphered(?string $data, string $password, int $id) : ?string
    {
        return is_null($data) ? null : $this->cipher->encrypt($data, $password, $id);
    }

    private function getAgreement(int $id) : HardwareAgreement
    {
        $repo = $this->em->getRepository(HardwareAgreement::class);
        /** @var HardwareAgreement|null $agreement */
        $agreement = $repo->find($id);
        if (is_null($agreement)) {
            throw new NotFoundHttpException("Agreement with given ID has not been found");
        }
        return $agreement;
    }
}
