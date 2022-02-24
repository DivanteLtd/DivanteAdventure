<?php

namespace Divante\Bundle\AdventureBundle\Services\FilterParser\Structures;

class TwoArgExpression implements Structure
{
    private Structure $left;
    private Structure $right;
    private TwoArgOperand $operand;

    public function __construct(Structure $leftSide, Structure $rightSide, TwoArgOperand $operand)
    {
        $this->left = $leftSide;
        $this->right = $rightSide;
        $this->operand = $operand;
    }

    public function toSql(): string
    {
        return sprintf("(%s %s %s)", $this->left->toSql(), $this->operand->toSql(), $this->right->toSql());
    }
}
