<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 17.01.19
 * Time: 07:39
 */

namespace Divante\Bundle\AdventureBundle\Entity\Repository;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Doctrine\ORM\EntityRepository;

class LeaveRequestDayRepository extends EntityRepository
{
    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @param Employee|null $employee
     * @return LeaveRequestDay[]
     */
    public function getLeaveRequestDays(\DateTime $start, \DateTime $end, ?Employee $employee = null) : array
    {
        $builder = $this->createQueryBuilder('lrd');
        if ($start !== -1) {
            $builder->andWhere('lrd.date >= :from')->setParameter('from', $start->setTime(0, 0, 0));
        }
        if ($end !== -1) {
            $builder->andWhere('lrd.date <= :to')->setParameter('to', $end->setTime(23, 59, 59));
        }
        if (!is_null($employee)) {
            $builder
                ->join('lrd.request', 'lr')
                ->join('lr.period', 'p')
                ->andWhere('p.employee = :employee')->setParameter('employee', $employee);
        }
        $query = $builder->getQuery();
        return $query->getResult();
    }

    /** @return LeaveRequestDay[] */
    public function getDaysToSendToAvaza(): array
    {
        $builder = $this->createQueryBuilder('lrd');
        $query = $builder
            ->andWhere('lrd.avazaSyncStatus = :avazaStatus')
            ->setParameter('avazaStatus', LeaveRequestDay::AVAZA_STATUS_NOT_SYNCED)
            ->join('lrd.employee', 'e')
            ->andWhere('e.avazaId IS NOT NULL')
            ->andWhere('lrd.status IN (:dayStatuses)')
            ->setParameter('dayStatuses', [ LeaveRequestDay::DAY_STATUS_ACTIVE ])
            ->join('lrd.request', 'lr')
            ->andWhere('lr.status IN (:requestStatuses)')
            ->setParameter('requestStatuses', [ LeaveRequest::REQUEST_STATUS_ACCEPTED ])
            ->getQuery();
        return $query->getResult();
    }

    /** @return LeaveRequestDay[] */
    public function getDaysToRemoveFromAvaza(): array
    {
        $builder = $this->createQueryBuilder('lrd');
        $query = $builder
            ->andWhere('lrd.avazaSyncStatus = :avazaStatus')
            ->andWhere('lrd.avazaId IS NOT NULL')
            ->setParameter('avazaStatus', LeaveRequestDay::AVAZA_STATUS_SYNCED)
            ->join('lrd.employee', 'e')
            ->andWhere('e.avazaId IS NOT NULL')
            ->join('lrd.request', 'lr')
            ->andWhere(
                $builder->expr()->orX(
                    $builder->expr()->in(
                        'lrd.status',
                        [ LeaveRequestDay::DAY_STATUS_CANCELED, LeaveRequestDay::DAY_STATUS_RESIGNED ]
                    ),
                    $builder->expr()->in(
                        'lr.status',
                        [ LeaveRequest::REQUEST_STATUS_RESIGNED ]
                    ),
                ),
            )
            ->getQuery();
        return $query->getResult();
    }
}
