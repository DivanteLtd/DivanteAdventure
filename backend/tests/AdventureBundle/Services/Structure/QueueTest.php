<?php

namespace Tests\AdventureBundle\Services\Structure;

use Divante\Bundle\AdventureBundle\Services\Structure\Queue;
use Divante\Bundle\AdventureBundle\Services\Structure\StructureException;

class QueueTest extends AbstractQueueTestBase
{
    /** @throws StructureException */
    public function testOneElementConstructorQueue() : void
    {
        $array = [
            $this->generateRandomElement(),
        ];
        $queue = new Queue($array);
        $this->assertEquals($array[0], $queue->get());
    }

    public function testOneElementConstructorCount() : void
    {
        $array = [
            $this->generateRandomElement(),
        ];
        $queue = new Queue($array);
        $this->assertCount(count($array), $queue);
    }

    /** @throws StructureException */
    public function testMultipleElementsConstructorQueue() : void
    {
        $array = [
            $this->generateRandomElement(),
            $this->generateRandomElement(),
            $this->generateRandomElement(),
        ];
        $queue = new Queue($array);
        $this->assertEquals($array[0], $queue->get());
        $this->assertEquals($array[1], $queue->get());
        $this->assertEquals($array[2], $queue->get());
    }

    public function testMultipleElementsConstructorCount() : void
    {
        $array = [
            $this->generateRandomElement(),
            $this->generateRandomElement(),
            $this->generateRandomElement(),
        ];
        $queue = new Queue($array);
        $this->assertCount(count($array), $queue);
    }

    /** @throws StructureException */
    public function testPuttingOneElement() : void
    {
        $queue = new Queue();
        $this->assertCount(0, $queue);

        $val1 = $this->generateRandomElement();
        $queue->put($val1);
        $this->assertCount(1, $queue);
        $receivedVal1 = $queue->get();
        $this->assertEquals($val1, $receivedVal1);
        $this->assertCount(0, $queue);
    }

    /** @throws StructureException */
    public function testPuttingThreeElements() : void
    {
        $queue = new Queue();
        $this->assertCount(0, $queue);

        $val1 = $this->generateRandomElement();
        $val2 = $this->generateRandomElement();
        $val3 = $this->generateRandomElement();
        $queue->put($val1)->put($val2)->put($val3);
        $this->assertCount(3, $queue);
        $receivedVal1 = $queue->get();
        $receivedVal2 = $queue->get();
        $receivedVal3 = $queue->get();
        $this->assertEquals($val1, $receivedVal1);
        $this->assertEquals($val2, $receivedVal2);
        $this->assertEquals($val3, $receivedVal3);
        $this->assertCount(0, $queue);
    }

    /** @throws StructureException */
    public function testExceptionOnEmptyStructure() : void
    {
        $queue = new Queue();
        $this->expectException(StructureException::class);
        $queue->get();
    }
}
