<?php
/**
 * @package  Divante\Bundle
 * @author BH <bh@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\Bundle\AdventureBundle\Auth\Locker\Generator;

class SimpleLockGenerator implements LockGeneratorInterface
{
    public function generate(
        int $length = 10,
        string $elements = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ): string {
        $pieces = [];
        $max = mb_strlen($elements, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces [] = $elements[ random_int(0, $max) ];
        }

        return implode('', $pieces);
    }
}
