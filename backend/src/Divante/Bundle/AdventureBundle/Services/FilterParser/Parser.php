<?php

namespace Divante\Bundle\AdventureBundle\Services\FilterParser;

use Divante\Bundle\AdventureBundle\Exception\ParserException;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\Column;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\FunctionSeparator;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\Parenthesis;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\PrimitiveValue;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\SqlFunction;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\Structure;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\TwoArgExpression;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Structures\TwoArgOperand;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Tokenizer\Token;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Tokenizer\Tokenizer;
use Divante\Bundle\AdventureBundle\Services\Structure\Queue;
use Divante\Bundle\AdventureBundle\Services\Structure\Stack;

/**
 * @inheritDoc
 */
class Parser implements ParserInterface
{
    private Tokenizer $tokenizer;

    public function __construct(Tokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    /** @inheritDoc */
    public function parse(string $filterInput, array $fields): string
    {
        $tokens = $this->tokenizer->tokenize($filterInput);
        // creating basic structures
        $structures = $this->convertToBaseStructures($tokens, $fields);
        $structuresStack = $this->runShuntingYardAlgorithm($structures);
        if (count($structuresStack) > 0) {
            $baseStructure = $this->handleTokenStack($structuresStack);
            return $baseStructure->toSql();
        }
        return '';
    }

    /**
     * @param Token[] $tokens
     * @param Field[] $fields
     * @return Structure[]
     */
    private function convertToBaseStructures(array $tokens, array $fields) : array
    {
        return array_map(fn(Token $token) : Structure => $token->convertToBaseStructure($fields), $tokens);
    }

    /**
     * @param Structure[] $structures
     * @return Stack
     * @throws \Divante\Bundle\AdventureBundle\Services\Structure\StructureException
     */
    private function runShuntingYardAlgorithm(array $structures) : Stack
    {
        $queue = new Queue($structures);
        $output = new Stack();
        $ops = new Stack();
        while (count($queue) > 0) {
            $token = $queue->get();
            if ($token instanceof PrimitiveValue || $token instanceof Column) {
                $output->put($token);
            }
            if ($token instanceof SqlFunction) {
                $ops->put($token);
            }
            if ($token instanceof TwoArgOperand) {
                while (count($ops) > 0 && (
                    ($ops[0] instanceof TwoArgOperand && $ops[0]->getPrecendence() < $token->getPrecendence()) ||
                    ($ops[0] instanceof SqlFunction)
                    )) {
                    $output->put($ops->get());
                }
                $ops->put($token);
            }
            if ($token instanceof Parenthesis && $token->isLeft()) {
                $ops->put($token);
            }
            if ($token instanceof FunctionSeparator || ($token instanceof Parenthesis && $token->isRight())) {
                while (!($ops[0] instanceof Parenthesis && $ops[0]->isLeft())) {
                    $output->put($ops->get());
                }
                // if token directly before parenthesis is a function, increment count of arguments
                if ($ops[1] instanceof SqlFunction) {
                    $ops[1]->incrementArgumentsCount();
                }
                // discarding left parenthesis if current token is right parenthesis
                if ($token instanceof Parenthesis) {
                    $ops->get();
                }
            }
        }
        while (count($ops) > 0) {
            $output->put($ops->get());
        }
        return $output;
    }

    /**
     * @param Stack $structures
     * @return Structure
     * @throws ParserException
     * @throws \Divante\Bundle\AdventureBundle\Services\Structure\StructureException
     */
    private function handleTokenStack(Stack $structures) : Structure
    {
        /** @var Structure $token */
        $token = $structures->get();
        if ($token instanceof PrimitiveValue || $token instanceof Column) {
            return $token;
        }
        if ($token instanceof TwoArgOperand) {
            $rightValue = $this->handleTokenStack($structures);
            $leftValue = $this->handleTokenStack($structures);
            return new TwoArgExpression($leftValue, $rightValue, $token);
        }
        if ($token instanceof SqlFunction) {
            $arguments = new Stack();
            $argumentsCount = $token->getArgumentsCount() + ($token->requiresValueBefore() ? 1 : 0);
            for ($i = 0; $i < $argumentsCount; $i++) {
                $arguments->put($this->handleTokenStack($structures));
            }
            $token->setArgumentValues($arguments->toArray());
            return $token;
        }
        throw new ParserException("Unrecognized token type when handling token stack");
    }
}
