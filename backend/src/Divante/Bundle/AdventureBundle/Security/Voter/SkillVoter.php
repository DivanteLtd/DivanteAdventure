<?php

namespace Divante\Bundle\AdventureBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\HttpFoundation\Request;
use Divante\Bundle\AdventureBundle\Entity\Skill;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class SkillVoter extends Voter
{
    const SKILL_LIST = 'SKILL_LIST';
    const SKILL_SEARCH = 'SKILL_SEARCH';
    const SKILL_RECOMMENDED = 'SKILL_RECOMMENDED';
    const SKILL_NEW = 'SKILL_NEW';
    const SKILL_SHOW = 'SKILL_SHOW';
    const SKILL_EDIT = 'SKILL_EDIT';
    const SKILL_DELETE = 'SKILL_DELETE';

    /** @var AccessDecisionManagerInterface  */
    private $decisionManager;

    /**
     * SkillVoter constructor.
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
        return $subject instanceof Skill && in_array(
            $attribute,
            [self::SKILL_SHOW, self::SKILL_EDIT, self::SKILL_DELETE]
        )
            || $subject instanceof Request && in_array(
                $attribute,
                [self::SKILL_NEW]
            )
            || $subject == null && in_array(
                $attribute,
                [self::SKILL_LIST, self::SKILL_SEARCH, self::SKILL_RECOMMENDED]
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
            case self::SKILL_LIST:
                return $user->hasRole('ROLE_USER');
            case self::SKILL_SEARCH:
                return $user->hasRole('ROLE_USER');
            case self::SKILL_RECOMMENDED:
                return $user->hasRole('ROLE_USER');
            case self::SKILL_NEW:
                return $user->hasRole('ROLE_USER');
            case self::SKILL_SHOW:
                return $user->hasRole('ROLE_USER');
            case self::SKILL_EDIT:
                return $this->canModify($user, $subject);
            case self::SKILL_DELETE:
                return $this->canModify($user, $subject);
            default:
                return false;
        }
    }

    /**
     * @param User $user
     * @param Skill $skill
     * @return bool
     */
    private function canModify(User $user, Skill $skill)
    {
        if ($user->hasRole('ROLE_TRIBE_MASTER')) {
            return true;
        }

        if ($user->hasRole('ROLE_USER') && $user->getEmployeeId() == $skill->getEmployee()->getId()) {
            return true;
        }

        return false;
    }
}
