<?php

namespace Divante\Bundle\AdventureBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\HttpFoundation\Request;
use Divante\Bundle\AdventureBundle\Entity\EmployeeSkillArea;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class EmployeeSkillAreaVoter extends Voter
{
    const EMPLOYEE_SKILL_AREA_LIST = 'EMPLOYEE_SKILL_AREA_LIST';
    const EMPLOYEE_SKILL_AREA_SEARCH = 'EMPLOYEE_SKILL_AREA_SEARCH';
    const EMPLOYEE_SKILL_AREA_NEW = 'EMPLOYEE_SKILL_AREA_NEW';
    const EMPLOYEE_SKILL_AREA_SHOW = 'EMPLOYEE_SKILL_AREA_SHOW';
    const EMPLOYEE_SKILL_AREA_EDIT = 'EMPLOYEE_SKILL_AREA_EDIT';
    const EMPLOYEE_SKILL_AREA_DELETE = 'EMPLOYEE_SKILL_AREA_DELETE';

    /** @var AccessDecisionManagerInterface  */
    private $decisionManager;

    /**
     * EmployeeSkillAreaVoter constructor.
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
        return $subject instanceof EmployeeSkillArea && in_array(
            $attribute,
            [self::EMPLOYEE_SKILL_AREA_SHOW, self::EMPLOYEE_SKILL_AREA_EDIT, self::EMPLOYEE_SKILL_AREA_DELETE]
        )
            || $subject instanceof Request && in_array(
                $attribute,
                [self::EMPLOYEE_SKILL_AREA_NEW]
            )
            || $subject == null && in_array(
                $attribute,
                [self::EMPLOYEE_SKILL_AREA_LIST, self::EMPLOYEE_SKILL_AREA_SEARCH]
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
            case self::EMPLOYEE_SKILL_AREA_LIST:
                return $user->hasRole('ROLE_USER');
            case self::EMPLOYEE_SKILL_AREA_SEARCH:
                return $user->hasRole('ROLE_USER');
            case self::EMPLOYEE_SKILL_AREA_NEW:
                return $this->canNew($user, $subject);
            case self::EMPLOYEE_SKILL_AREA_SHOW:
                return $user->hasRole('ROLE_USER');
            case self::EMPLOYEE_SKILL_AREA_EDIT:
                return $this->canModify($user, $subject);
            case self::EMPLOYEE_SKILL_AREA_DELETE:
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
     * @param EmployeeSkillArea $employeeSkillArea
     * @return bool
     */
    private function canModify(User $user, EmployeeSkillArea $employeeSkillArea)
    {
        if ($user->hasRole('ROLE_TRIBE_MASTER')) {
            return true;
        }

        if ($user->hasRole('ROLE_USER') && $user->getEmployeeId() == $employeeSkillArea->getEmployee()->getId()) {
            return true;
        }

        return false;
    }
}
