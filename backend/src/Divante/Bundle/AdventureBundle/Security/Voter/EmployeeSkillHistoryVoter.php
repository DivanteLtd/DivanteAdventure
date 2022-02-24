<?php

namespace Divante\Bundle\AdventureBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\HttpFoundation\Request;
use Divante\Bundle\AdventureBundle\Entity\EmployeeSkillHistory;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class EmployeeSkillHistoryVoter extends Voter
{
    const EMPLOYEE_SKILL_HISTORY_LIST = 'EMPLOYEE_SKILL_HISTORY_LIST';
    const EMPLOYEE_SKILL_HISTORY_SEARCH = 'EMPLOYEE_SKILL_HISTORY_SEARCH';
    const EMPLOYEE_SKILL_HISTORY_NEW = 'EMPLOYEE_SKILL_HISTORY_NEW';
    const EMPLOYEE_SKILL_HISTORY_SHOW = 'EMPLOYEE_SKILL_HISTORY_SHOW';
    const EMPLOYEE_SKILL_HISTORY_EDIT = 'EMPLOYEE_SKILL_HISTORY_EDIT';
    const EMPLOYEE_SKILL_HISTORY_DELETE = 'EMPLOYEE_SKILL_HISTORY_DELETE';

    /** @var AccessDecisionManagerInterface  */
    private $decisionManager;

    /**
     * EmployeeSkillHistoryVoter constructor.
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
        return $subject instanceof EmployeeSkillHistory && in_array(
            $attribute,
            [self::EMPLOYEE_SKILL_HISTORY_SHOW, self::EMPLOYEE_SKILL_HISTORY_EDIT, self::EMPLOYEE_SKILL_HISTORY_DELETE]
        )
            || $subject instanceof Request && in_array(
                $attribute,
                [self::EMPLOYEE_SKILL_HISTORY_NEW]
            )
            || $subject == null && in_array(
                $attribute,
                [self::EMPLOYEE_SKILL_HISTORY_LIST, self::EMPLOYEE_SKILL_HISTORY_SEARCH]
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
            case self::EMPLOYEE_SKILL_HISTORY_LIST:
                return $user->hasRole('ROLE_USER');
            case self::EMPLOYEE_SKILL_HISTORY_SEARCH:
                return $user->hasRole('ROLE_USER');
            case self::EMPLOYEE_SKILL_HISTORY_NEW:
                return $this->canNew($user, $subject);
            case self::EMPLOYEE_SKILL_HISTORY_SHOW:
                return $user->hasRole('ROLE_USER');
            case self::EMPLOYEE_SKILL_HISTORY_EDIT:
                return $user->hasRole('ROLE_TRIBE_MASTER');
            case self::EMPLOYEE_SKILL_HISTORY_DELETE:
                return $user->hasRole('ROLE_TRIBE_MASTER');
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
}
