<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 16.01.19
 * Time: 14:25
 */

namespace Divante\Bundle\AdventureBundle\Infrastructure\Doctrine\Dbal;

use Divante\Bundle\AdventureBundle\Query\Pairings\PairingsQuery;
use Divante\Bundle\AdventureBundle\Query\Pairings\PairingsView;
use Doctrine\ORM\EntityManagerInterface;

class DbalPairingsQuery implements PairingsQuery
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /** @inheritdoc */
    public function getByTimestamps(int $start, int $stop) :array
    {
        $pairings = $this->em->createQueryBuilder()
            ->select(
                'eo.id, eo.occupancy, e.id as employee_id,
                 p.id as project_id, eo.date, p.endedAt as ended_at, p.archived'
            )
            ->from('AdventureBundle:EmployeeOccupancy', 'eo')
            ->innerJoin('eo.project', 'p')
            ->innerJoin('eo.employee', 'e')
            ->where('eo.date >= :start')
            ->andWhere('eo.date <= :stop')
            ->setParameters(['start' => $start, 'stop' => $stop])
            ->getQuery()
            ->getResult();
        return array_map(function ($pair) {
            return new PairingsView(
                $pair['id'],
                $pair['employee_id'],
                $pair['project_id'],
                $pair['occupancy'],
                $pair['date'],
                $pair['ended_at'],
                $pair['archived']
            );
        }, $pairings);
    }

    /** @inheritDoc */
    public function getByData(array $data): PairingsView
    {
        $pair = $this->em->createQueryBuilder()
            ->select(
                'eo.id, eo.occupancy, e.id as employee_id, p.id as project_id,
                 eo.date, p.endedAt as ended_at, p.archived'
            )
            ->from('AdventureBundle:EmployeeOccupancy', 'eo')
            ->innerJoin('eo.project', 'p')
            ->innerJoin('eo.employee', 'e')
            ->where('e.id = :eid')
            ->andWhere('p.id = :pid')
            ->andWhere('eo.occupancy = :oc')
            ->andWhere('eo.date = :da')
            ->setParameters(
                [
                    'eid' => $data['employee_id'],
                    'pid' => $data['project_id'],
                    'oc'  => $data['occupancy'],
                    'da'  => $data['date']
                ]
            )
            ->getQuery()
            ->getResult();

        return new PairingsView(
            $pair[0]['id'],
            $pair[0]['employee_id'],
            $pair[0]['project_id'],
            $pair[0]['occupancy'],
            $pair[0]['date'],
            $pair[0]['ended_at'],
            $pair[0]['archived']
        );
    }
}
