<?php
/**
 * @package  Divante\Bundle
 * @author BH <bh@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\Bundle\AdventureBundle\Auth\Locker;

use Divante\Bundle\AdventureBundle\Entity\User;

/**
 * Interface LockerInterface
 */
interface LockerManagerInterface
{
    /**
     * @param int $length
     *
     * @return string
     */
    public function generateLock(int $length = 10): string;

    /**
     * @param User $user
     * @param string $code
     *
     * @return bool
     */
    public function isCodeValid(User $user, string $code): bool;
}
