<?php

namespace Divante\Bundle\AdventureBundle\Command;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Divante\Bundle\AdventureBundle\Supplier\FreeDaysSupplier;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

class ClearLeave extends Command
{
    private EntityManagerInterface $em;
    private FreeDaysSupplier $supplier;

    public function __construct(EntityManagerInterface $em, FreeDaysSupplier $supplier)
    {
        $this->em = $em;
        $this->supplier = $supplier;
        parent::__construct();
    }


    /**
     * @inheritdoc
     */
    protected function configure() : void
    {
        $this
            ->setName('adventure:clear:leave')
            ->setDescription('Clears leave day from saturdays, sundays an free days.');
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->em->getRepository(LeaveRequestDay::class);
        $record = $repo->findAll();
        /** @var LeaveRequestDay $item */
        foreach ($record as $item) {
            $day = $item->getDate();
            $numberDay = (int)$day->format('w');
            if ($numberDay === 6 || $numberDay === 0) {
                $this->em->remove($item);
            }
            $freeDays = $this->supplier->getFreeDays(
                (int)$day->format('Y'),
                (int)$day->format('Y')
            );
            $freeDaysArray = array_filter($freeDays, function ($dayLocal) use ($day) {
                $dayLocalDateTime = new \DateTime($dayLocal);
                $diff = $dayLocalDateTime->diff($day);
                if ($diff->format('%a') !== '0') {
                    return false;
                }
                return true;
            });
            if (!empty($freeDaysArray)) {
                $this->em->remove($item);
            }
        }

        $this->em->flush();
        return 0;
    }
}
