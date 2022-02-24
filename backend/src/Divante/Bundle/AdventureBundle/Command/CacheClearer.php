<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 28.01.19
 * Time: 13:32
 */

namespace Divante\Bundle\AdventureBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CacheClearer extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure() : void
    {
        $this
            ->setName('adventure:cache:clear')
            ->setDescription('Clears additional cache. Run after migrations.');
    }

    /**
     * @inheritdoc
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $output->write("Clearing cache... ");
        $this->getContainer()->get('cache.app')->clear();
        $output->writeln("done.");
        return 0;
    }
}
