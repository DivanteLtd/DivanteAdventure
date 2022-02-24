<?php

namespace Divante\Bundle\AdventureBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\HttpFoundation\Request;
use Divante\Bundle\AdventureBundle\Entity\EmployeeSkill;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class EmployeeSkillVoter extends Voter
{
    const EMPLOYEE_SKILL_LIST = 'EMPLOYEE_SKILL_LIST';
    const EMPLOYEE_SKILL_SEARCH = 'EMPLOYEE_SKILL_SEARCH';
    const EMPLOYEE_SKILL_RECOMMENDED = 'EMPLOYEE_SKILL_RECOMMENDED';
    const EMPLOYEE_SKILL_NEW = 'EMPLOYEE_SKILL_NEW';
    const EMPLOYEE_SKILL_SHOW = 'EMPLOYEE_SKILL_SHOW';
    const EMPLOYEE_SKILL_EDIT = 'EMPLOYEE_SKILL_EDIT';
    const EMPLOYEE_SKILL_DELETE = 'EMPLOYEE_SKILL_DELETE';

    /** @var AccessDecisionManagerInterface  */
    private $decisionManager;

    /**
     * EmployeeSkillVoter constructor.
     * @param AccessDecisionManagerInterface $decisionManager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }
    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof EmployeeSkill && in_array(
            $attribute,
            [self::EMPLOYEE_SKILL_SHOW, self::EMPLOYEE_SKILL_EDIT, self::EMPLOYEE_SKILL_DELETE]
        )
            || $subject instanceof Request && in_array(
                $attribute,
                [self::EMPLOYEE_SKILL_NEW]
            )
            || $subject == null && in_array(
                $attribute,
                [self::EMPLOYEE_SKILL_LIST, self::EMPLOYEE_SKILL_SEARCH, self::EMPLOYEE_SKILL_RECOMMENDED]
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

        if ($this->decisionManager->decide($token, ['ROLE_USER'])) {
            return true;
        }

        switch ($attribute) {
            case self::EMPLOYEE_SKILL_LIST:
                return $user->hasRole('ROLE_USER');
            case self::EMPLOYEE_SKILL_SEARCH:
                return $user->hasRole('ROLE_USER');
            case self::EMPLOYEE_SKILL_RECOMMENDED:
                return $user->hasRole('ROLE_USER');
            case self::EMPLOYEE_SKILL_NEW:
                return $this->canNew($user, $subject);
            case self::EMPLOYEE_SKILL_SHOW:
                return $user->hasRole('ROLE_USER');
            case self::EMPLOYEE_SKILL_EDIT:
                return $this->canModify($user, $subject);
            case self::EMPLOYEE_SKILL_DELETE:
                return $this->canModify($user, $subject);
            default:
                return false;
        }
    }

    /**
     * @param User $user
     * @param Request $request
     * @return bool
     */
    private function canNew(User $user, Request $request)
    {
        if ($user->hasRole('ROLE_TRIBE_MASTER')) {
            return true;
        }

        $data = json_decode($request->getContent(), true);
        if (empty($data['employee'])) {
            return false;
        }

        if ($user->hasRole('ROLE_USER') && $user->getEmployeeId() == $data['employee']) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param EmployeeSkill $employeeSkill
     * @return bool
     */
    private function canModify(User $user, EmployeeSkill $employeeSkill)
    {
        if ($user->hasRole('ROLE_TRIBE_MASTER')) {
            return true;
        }

        if ($user->hasRole('ROLE_USER') && $user->getEmployeeId() == $employeeSkill->getEmployee()->getId()) {
            return true;
        }

        return false;
    }
}
