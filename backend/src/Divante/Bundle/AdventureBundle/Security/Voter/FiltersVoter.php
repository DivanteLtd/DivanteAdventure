<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 28.12.18
 * Time: 12:22
 */

namespace Divante\Bundle\AdventureBundle\Security\Voter;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class FiltersVoter extends Voter
{
    const FILTER_LIST = 'FILTER_LIST';

    /** @var AccessDecisionManagerInterface  */
    private $decisionManager;

    /**
     * FiltersVoter constructor.
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
        return $subject == null && in_array($attribute, [self::FILTER_LIST]);
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
        switch ($attribute) {
            case self::FILTER_LIST:
                return true;
            default:
                return false;
        }
    }
}
