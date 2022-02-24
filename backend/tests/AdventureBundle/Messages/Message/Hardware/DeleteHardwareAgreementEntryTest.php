<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Tests\AdventureBundle\Messages\Message\Hardware;

use Divante\Bundle\AdventureBundle\Message\Hardware\DeleteHardwareAgreementEntry;
use Tests\FoundationTestCase;

class DeleteHardwareAgreementEntryTest extends FoundationTestCase
{
    public function testCorrectIdStored() : void
    {
        $id = rand(0, 10000);
        $message = new DeleteHardwareAgreementEntry($id);
        $this->assertEquals($id, $message->getId());
    }
}