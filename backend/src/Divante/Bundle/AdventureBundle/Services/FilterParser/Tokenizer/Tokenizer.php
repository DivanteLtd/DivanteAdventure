<?php

namespace Divante\Bundle\AdventureBundle\Services\FilterParser\Tokenizer;

use Divante\Bundle\AdventureBundle\Exception\ParserException;

class Tokenizer
{
    /**
     * @param string $text
     * @return Token[]
     * @throws ParserException
     */
    public function tokenize(string $text) : array
    {
        $tokens = [];
        $text = trim($text);
        do {
            $actionDone = false;
            foreach (Token::RULES as $tokenRule) {
                $tokenName = $tokenRule['name'];
                $tokenRegex = $tokenRule['regex'];
                if (preg_match($tokenRegex, $text, $matches)) {
                    $match = $matches[0];
                    $text = trim(substr($text, strlen($match)));
                    $actionDone = true;
                    if (array_key_exists('substring_end', $tokenRule)) {
                        $match = substr($match, $tokenRule['substring_start'] ?? 0, -$tokenRule['substring_end']);
                    } else {
                        $match = substr($match, $tokenRule['substring_start'] ?? 0);
                    }
                    $match = str_replace(["\\\"", "\'"], ["\"", "'"], $match);
                    $tokens[] = new Token($tokenName, $match);
                    break;
                }
            }
        } while (strlen($text) > 0 && $actionDone);
        if (strlen($text) > 0) {
            throw new ParserException("Tokenizer error: unrecognized symbols at $text");
        }
        return $tokens;
    }
}
