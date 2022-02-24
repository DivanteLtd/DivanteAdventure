<?php

namespace Divante\Bundle\AdventureBundle\Services\Hardware;

class PasswordGenerator
{
    public const BLOCKS_COUNT = 3; // count of blocks bordered by minus character.
    public const BLOCK_LENGTH = 6; // amount of characters in single block.

    /**
     * @return string
     * @throws \Exception
     */
    public function generatePassword() : string
    {
        $password = "";
        for ($i = 0; $i < self::BLOCKS_COUNT; $i++) {
            $password .= $this->generateBlock();
        }
        return wordwrap($password, self::BLOCK_LENGTH, '-', true);
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function generateBlock() : string
    {
        $length = self::BLOCK_LENGTH;
        if (self::BLOCK_LENGTH % 2 == 1) {
            $length++;
        }
        $block = bin2hex(random_bytes($length / 2));
        if (self::BLOCK_LENGTH % 2 == 1) {
            $block = substr($block, 1);
        }
        return $block;
    }
}
