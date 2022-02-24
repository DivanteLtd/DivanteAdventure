<?php

namespace Divante\Bundle\AdventureBundle\Command;

use Divante\Bundle\AdventureBundle\Entity\EmployeeOccupancy;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Divante\Bundle\AdventureBundle\Supplier\FreeDaysSupplier;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

class ClearOccupancy extends Command
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
            ->setName('adventure:clear:occupancy')
            ->setDescription('Clears occupancy from saturdays, sundays an free days.');
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->em->getRepository(EmployeeOccupancy::class);
        $record = $repo->findAll();
        /** @var EmployeeOccupancy $item */
        foreach ($record as $item) {
            $day = (new \DateTime())->setTimestamp($item->getDate());
            $numberDay = (int)$day->format('w');
            if ($numberDay === 6 || $numberDay === 0) {
                $item->setOccupancy(0);
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
                $item->setOccupancy(0);
            }
        }

        $this->em->flush();
        $this->clearOccupancyFromLeaveDays($output);
        return 0;
    }

    private function clearOccupancyFromLeaveDays(OutputInterface $output) :void
    {
        $idsToDelete = [];
        $qb = $this->em->createQueryBuilder();
        $qb->select('eo.date as date, eo.occupancy/3600 as occupancy, eo.id as id, employee.id as e_id')
           ->from('AdventureBundle:EmployeeOccupancy', 'eo')
            ->innerJoin('eo.employee', 'employee')
            ->where('eo.occupancy <> 0')
            ->orderBy('eo.date');
        $res = $qb->getQuery()->getResult();
        foreach ($res as $item) {
            $date = new \DateTime();
            $date->setTimestamp($item['date']);
            $qblrd = $this->em->createQueryBuilder();
            $qblrd->select('lrd.id')
                ->from('AdventureBundle:LeaveRequestDay', 'lrd')
                ->andWhere('lrd.status IN (0,2)')
                ->andWhere('request.status = 1')
                ->andWhere('lrd.date = :date')
                ->andWhere('lrd.employee = :employee')
                ->setParameter('date', $date->format('Y-m-d'))
                ->setParameter('employee', $item['e_id'])
                ->innerJoin('lrd.request', 'request');
            if (!empty($qblrd->getQuery()->getResult())) {
                $output->writeln(sprintf('%s %s %s', $item['id'], $date->format('Y-m-d'), $item['occupancy']));
                $idsToDelete[] = $item['id'];
            }
        }
        $builder = $this->em->createQueryBuilder();
        $builder->update('AdventureBundle:EmployeeOccupancy', 'eo1')
            ->set('eo1.occupancy', 0)
            ->where('eo1.id IN (:ids)')
            ->setParameter('ids', $idsToDelete, Connection::PARAM_INT_ARRAY);
        $builder->getQuery()->execute();
    }
}
