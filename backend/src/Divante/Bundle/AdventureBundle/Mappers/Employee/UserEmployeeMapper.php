<?php

namespace Divante\Bundle\AdventureBundle\Mappers\Employee;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\LeavePeriod;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Divante\Bundle\AdventureBundle\Entity\Level;
use Divante\Bundle\AdventureBundle\Entity\Position;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Mappers\LevelMapper;
use Divante\Bundle\AdventureBundle\Mappers\PositionMapper;
use Doctrine\Common\Collections\Collection;

class UserEmployeeMapper extends AbstractEmployeeMapper
{
    private PositionMapper $positionMapper;
    private LevelMapper $levelMapper;
    private MinimalisticEmployeeMapper $minMapper;
    private ContractMapper $contractMapper;

    public function __construct(
        PositionMapper $positionMapper,
        LevelMapper $levelMapper,
        MinimalisticEmployeeMapper $minMapper,
        ContractMapper $contractMapper
    ) {
        $this->positionMapper = $positionMapper;
        $this->levelMapper = $levelMapper;
        $this->minMapper = $minMapper;
        $this->contractMapper = $contractMapper;
    }

    /** @inheritDoc */
    public function mapEmployeeToJson(Employee $entity): array
    {
        return [
            "id" => $entity->getId(),
            "name" => $entity->getName(),
            "lastName" => $entity->getLastName(),
            "photo" => $entity->getPhoto(),
            "phone" => $entity->getPhone(),
            "position" => $this->getPosition($entity->getPosition()),
            "level" => $this->getLevel($entity->getLevel()),
            "tribe" => $this->getTribe($entity->getTribe()),
            "workMode" => $entity->getWorkMode(),
            "email" => $entity->getEmail(),
            'manager' => $entity->isManager(),
            "licencePlate" => $entity->getCar(),
            "gender" => $entity->getGender(),
            "freeToday" => $this->isFreeDayToday($entity),
            "leaders" => $this->getLeaders($entity->getLeaders()),
            "dataUpdate" => $this->getDate($entity->getDataUpdateTime()),
            "techTribeLeader" => $entity->isTechTribeLeader(),
            "student" => $entity->isStudent(),
            "taxDeductibleCosts" => $entity->getTaxDeductibleCosts(),
            "workStreet" => $entity->getWorkStreet(),
            "workCity" => $entity->getWorkCity(),
            "workPostalCode" => $entity->getWorkPostalCode(),
            "workCountry" => $entity->getWorkCountry(),
            "superiorEmail" => $this->getSuperiorEmail($entity->getSuperior()),
            "financeCode" => $entity->getFinanceCode(),
            "arrayContracts" => $this->getContracts($entity->getContracts()),
            "shoeSize" => $entity->getShoeSize(),
            "sweatshirtSize" => $entity->getSweatshirtSize(),
            "shirtSize" => $entity->getShirtSize(),
            "subTypeContract" => $entity->getOutsourceSubType()
        ];
    }

    /**
     * @param Position|null $position
     * @return null|array<string,mixed>
     */
    private function getPosition(?Position $position) : ?array
    {
        return is_null($position) ? null : $this->positionMapper->mapEntity($position);
    }

    /**
     * @param Level|null $level
     * @return null|array<string,mixed>
     */
    private function getLevel(?Level $level) : ?array
    {
        return is_null($level) ? null : $this->levelMapper->mapEntity($level);
    }

    /**
     * @param Tribe|null $tribe
     * @return null|array<string,mixed>
     */
    private function getTribe(?Tribe $tribe) : ?array
    {
        return is_null($tribe) ? null : [
            'id' => $tribe->getId(),
            'name' => $tribe->getName(),
            'description' => $tribe->getDescription(),
        ];
    }

    private function isFreeDayToday(Employee $employee) : bool
    {
        $period = $this->getCurrentPeriod($employee);
        if (is_null($period)) {
            return false;
        }
        $requests = $period->getRequests()->filter(
            function (LeaveRequest $request) {
                if ($request->getStatus() === LeaveRequest::REQUEST_STATUS_ACCEPTED
                    || $request->getStatus() === LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION
                ) {
                    $requestDaysToday = $request->getRequestDays()->filter(
                        function (LeaveRequestDay $requestDay) {
                            $dayStart = (new DateTime())->format('Y-m-d');
                            $dayEnd = (new DateTime())->format('Y-m-d');
                            return $dayStart <= $requestDay->getDate() && $dayEnd >= $requestDay->getDate();
                        }
                    );
                    return !$requestDaysToday->isEmpty();
                }
                return false;
            }
        );
        return !$requests->isEmpty();
    }

    private function getCurrentPeriod(Employee $employee) : ?LeavePeriod
    {
        $periods = $employee->getPeriods()->filter(
            function (LeavePeriod $period) {
                $timeFrom = $period->getDateFrom()->getTimestamp();
                $timeTo = $period->getDateTo()->getTimestamp();
                $timeCurrent = time();
                return $timeCurrent >= $timeFrom && $timeCurrent <= $timeTo;
            }
        );
        return $periods->isEmpty() ? null : $periods->first();
    }

    /**
     * @param Collection $leaders
     * @return null|array<int, array<string, mixed>>
     */
    private function getLeaders(Collection $leaders) : ?array
    {
        if (!$leaders->isEmpty()) {
            $mappedLeaders = [];
            /** @var Employee $leader */
            foreach ($leaders as $leader) {
                $mappedLeaders[] = $this->minMapper->mapEmployeeToJson($leader);
            }
            return $mappedLeaders;
        } else {
            return null;
        }
    }

    private function getDate(?DateTime $dateTime) : ?string
    {
        return is_null($dateTime) ? null : $dateTime->format('Y-m-d');
    }

    private function getSuperiorEmail(?Employee $superior): string
    {
        return is_null($superior) ? '' : $superior->getEmail();
    }

    private function getContracts(Collection $contracts): ?array
    {
        if (!$contracts->isEmpty()) {
            $mappedContracts = [];
            foreach ($contracts as $contract) {
                $mappedContracts[] = $this->contractMapper->mapContractToJson($contract);
            }
            return $mappedContracts;
        } else {
            return null;
        }
    }
}
