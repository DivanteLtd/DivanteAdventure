<?php
/**
 * @package  Divante\Bundle
 * @author BH <bh@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\Bundle\AdventureBundle\Auth;

use Divante\Bundle\AdventureBundle\Auth\Locker\LockerManagerInterface;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\EmailConfig;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailSenderInterface;
use Divante\Bundle\AdventureBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Translation\TranslatorInterface;

class UserPersistor
{
    private EmailSenderInterface $mailer;
    private ObjectManager $objectManager;
    private LockerManagerInterface $lockerManager;
    protected TranslatorInterface $translator;
    private string $techEmail;

    public function __construct(
        EmailSenderInterface $mailer,
        ObjectManager $objectManager,
        LockerManagerInterface $lockerManager,
        TranslatorInterface $translator,
        EmailConfig $emailConfig
    ) {
        $this->mailer = $mailer;
        $this->objectManager = $objectManager;
        $this->lockerManager = $lockerManager;
        $this->translator = $translator;
        $this->techEmail = $emailConfig->getTechEmail();
    }


    public function increaseErrorCounter(User $user) : User
    {
        $user->setLoginErrors($user->getLoginErrors() + 1);

        $this->persistUser($user);

        return $user;
    }

    protected function persistUser(User $user): void
    {
        $this->objectManager->merge($user);
        $this->objectManager->flush();
    }

    public function resetErrorCounter(User $user) : User
    {
        $user->setLoginErrors(0);

        $this->persistUser($user);

        return $user;
    }

    /**
     * @param User $user
     *
     * @return User
     * @throws \Exception
     */
    public function lockUser(User $user) : User
    {
        $user->setLocked($this->lockerManager->generateLock());
        $this->sendEmailToBlockedUser($user);
        $this->persistUser($user);

        return $user;
    }

    /**
     * @param User $user
     *
     * @return User
     * @throws \Exception
     */
    public function sendEmailToBlockedUser(User $user) :User
    {
        $employee = $user->getEmployee();
        $language = $user->getEmployee()->getLanguage();
        $this->translator->setLocale($language);
        $subject = $this->translator->trans('accountLocked');
        $this->mailer->sendMessage(
            $user->getEmail(),
            $this->techEmail,
            $subject,
            ['employee' => $employee],
            'AdventureBundle:Emails:account_lock_code.html.twig'
        );

        return $user;
    }


    public function tryToUnlockUser(User $user, string $code) : bool
    {
        if ($this->lockerManager->isCodeValid($user, $code)) {
            $this->unlockUser($user);

            return true;
        };

        return false;
    }

    public function unlockUser(User $user) : User
    {
        $user->setLocked(null);

        $this->persistUser($user);

        return $user;
    }


    public function persist(User $user, string $timeout) : User
    {
        $expDateTime = new \DateTime();
        $expDateTime->setTimestamp((int)$timeout);
        $user->setLoginExpiration($expDateTime);
        $user->setLoginErrors(0);
        $user->setLocked(null);

        $this->persistUser($user);

        return $user;
    }
}
