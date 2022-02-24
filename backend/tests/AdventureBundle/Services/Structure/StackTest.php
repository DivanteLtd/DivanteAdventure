<?php

namespace Tests\AdventureBundle\Services\Structure;

use Divante\Bundle\AdventureBundle\Services\Structure\Stack;
use Divante\Bundle\AdventureBundle\Services\Structure\StructureException;

class StackTest extends AbstractQueueTestBase
{
    /** @throws StructureException */
    public function testOneElementConstructorStack() : void
    {
        $array = [
            $this->generateRandomElement(),
        ];
        $stack = new Stack($array);
        $this->assertEquals($array[0], $stack->get());
    }

    public function testOneElementConstructorCount() : void
    {
        $array = [
            $this->generateRandomElement(),
        ];
        $stack = new Stack($array);
        $this->assertCount(count($array), $stack);
    }

    /** @throws StructureException */
    public function testMultipleElementsConstructorStack() : void
    {
        $array = [
            $this->generateRandomElement(),
            $this->generateRandomElement(),
            $this->generateRandomElement(),
        ];
        $stack = new Stack($array);
        $this->assertEquals($array[0], $stack->get());
        $this->assertEquals($array[1], $stack->get());
        $this->assertEquals($array[2], $stack->get());
    }

    public function testMultipleElementsConstructorCount() : void
    {
        $array = [
            $this->generateRandomElement(),
            $this->generateRandomElement(),
            $this->generateRandomElement(),
        ];
        $stack = new Stack($array);
        $this->assertCount(count($array), $stack);
    }

    /** @throws StructureException */
    public function testPuttingOneElement() : void
    {
        $stack = new Stack();
        $this->assertCount(0, $stack);

        $val1 = $this->generateRandomElement();
        $stack->put($val1);
        $this->assertCount(1, $stack);
        $receivedVal1 = $stack->get();
        $this->assertEquals($val1, $receivedVal1);
        $this->assertCount(0, $stack);
    }

    /** @throws StructureException */
    public function testPuttingThreeElements() : void
    {
        $stack = new Stack();
        $this->assertCount(0, $stack);

        $val1 = $this->generateRandomElement();
        $val2 = $this->generateRandomElement();
        $val3 = $this->generateRandomElement();
        $stack->put($val1)->put($val2)->put($val3);
        $this->assertCount(3, $stack);
        $receivedVal3 = $stack->get();
        $receivedVal2 = $stack->get();
        $receivedVal1 = $stack->get();
        $this->assertEquals($val1, $receivedVal1);
        $this->assertEquals($val2, $receivedVal2);
        $this->assertEquals($val3, $receivedVal3);
        $this->assertCount(0, $stack);
    }

    /** @throws StructureException */
    public function testExceptionOnEmptyStructure() : void
    {
        $stack = new Stack();
        $this->expectException(StructureException::class);
        $stack->get();
    }
}
