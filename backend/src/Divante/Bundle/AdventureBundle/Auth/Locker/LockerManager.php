<?php
/**
 * @package  Divante\Bundle
 * @author BH <bh@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\Bundle\AdventureBundle\Auth\Locker;

use Divante\Bundle\AdventureBundle\Auth\Locker\Generator\LockGeneratorInterface;
use Divante\Bundle\AdventureBundle\Entity\User;

class LockerManager implements LockerManagerInterface
{
    private LockGeneratorInterface $lockGenerator;

    public function __construct(LockGeneratorInterface $lockGenerator)
    {
        $this->lockGenerator = $lockGenerator;
    }

    public function generateLock(int $length = 10): string
    {
        return $this->lockGenerator->generate($length);
    }

    public function isCodeValid(User $user, string $code): bool
    {
        return ($user->getLocked() === $code);
    }
}
