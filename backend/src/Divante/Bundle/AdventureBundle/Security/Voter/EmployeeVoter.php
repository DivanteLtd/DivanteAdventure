<?php

namespace Divante\Bundle\AdventureBundle\Security\Voter;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class EmployeeVoter extends Voter
{
    const EMPLOYEE_EDIT   = 'EMPLOYEE_EDIT';
    const EMPLOYEE_SELF   = 'EMPLOYEE_SELF';

    private AccessDecisionManagerInterface $decisionManager;
    private RequestStack $request;

    public function __construct(AccessDecisionManagerInterface $decisionManager, RequestStack $request)
    {
        $this->decisionManager = $decisionManager;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof Employee
               && in_array(
                   $attribute,
                   [self::EMPLOYEE_EDIT, self::EMPLOYEE_SELF]
               );
    }

    /**
     * @inheritdoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Employee $employee */
        $employee = $subject;
        if ($user->hasRole('ROLE_SUPER_ADMIN') || $user->hasRole('ROLE_TRIBE_MASTER')) {
            return true;
        }

        switch ($attribute) {
            case self::EMPLOYEE_EDIT:
                return $this->isSelf($user, $employee);
            case self::EMPLOYEE_SELF:
                return $this->isSelf($user, $employee);
            default:
                return false;
        }
    }

    private function isSelf(User $user, Employee $employee) : bool
    {
        return ($user->hasRole('ROLE_USER') && $user->getEmployeeId() == $employee->getId());
    }
}
