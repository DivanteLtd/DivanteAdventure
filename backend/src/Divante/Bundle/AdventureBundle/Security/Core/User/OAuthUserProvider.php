<?php

namespace Divante\Bundle\AdventureBundle\Security\Core\User;

use Divante\Bundle\AdventureBundle\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\UserInterface;

class OAuthUserProvider extends BaseClass
{
    public UserResponseInterface $socialResponse;

    /**
     * @param UserResponseInterface $response
     *
     * @return UserInterface
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $this->socialResponse = $response;

        $socialId = $response->getUsername();
        /** @var User|null $user */
        $user     = $this->userManager->findUserBy([ $this->getProperty($response) => $socialId ]);
        $email    = $response->getEmail();
        if (is_null($user)) {
            /** @var User|null $user */
            $user = $this->userManager->findUserByEmail($email);

            if (is_null($user) || !$user instanceof UserInterface) {
                /** @var User $user */
                $user = $this->userManager->createUser();
                $user->setUsername($email);
                $user->setUsernameCanonical($response->getLastName());
                $user->setEmail($email);
                $user->setPlainPassword(md5(uniqid()));
                $user->setEnabled(true);
            }

            $service = $response->getResourceOwner()->getName();
            switch ($service) {
                case 'google':
                    $user->setGoogleId($socialId);
                    break;
            }

            $this->userManager->updateUser($user);
        } else {
            $checker = new UserChecker();
            $checker->checkPreAuth($user);
        }

        return $user;
    }
}
