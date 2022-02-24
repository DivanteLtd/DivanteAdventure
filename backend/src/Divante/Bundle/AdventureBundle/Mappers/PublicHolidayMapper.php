<?php

namespace Divante\Bundle\AdventureBundle\Mappers;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\PublicHoliday;
use Divante\Bundle\AdventureBundle\Supplier\FreeDaysSupplier;

class PublicHolidayMapper
{
    private FreeDaysSupplier $supplier;
    private int $currentYear;

    public function __construct(FreeDaysSupplier $supplier)
    {
        $this->supplier = $supplier;
        $this->currentYear = (int)date('Y');
    }

    /**
     * @param PublicHoliday $holiday
     * @return array<string,mixed>
     */
    public function __invoke(PublicHoliday $holiday): array
    {
        $year = $this->currentYear;
        $date = $this->supplier->getDate($holiday, $year);
        while ($date < new DateTime() && $holiday->isRepeating()) {
            $date = $this->supplier->getDate($holiday, ++$year);
        }
        return [
            'id' => $holiday->getId(),
            'calculationType' => $holiday->getCalculationType(),
            'date' => $date->format('Y-m-d'),
            'name' => $holiday->getName(),
            'repeating' => $holiday->isRepeating(),
            'enabled' => $holiday->isEnabled(),
        ];
    }
}
