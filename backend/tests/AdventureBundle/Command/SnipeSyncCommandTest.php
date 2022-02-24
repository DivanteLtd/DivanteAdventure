<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Tests\AdventureBundle\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SnipeSyncCommandTest extends WebTestCase
{
       public function testCommandExists() : void
    {
        $kernel = self::createKernel();
        $application = new Application($kernel);
        $command = $application->find('adventure:snipe:sync');
        $this->assertNotNull($command);
    }
}
