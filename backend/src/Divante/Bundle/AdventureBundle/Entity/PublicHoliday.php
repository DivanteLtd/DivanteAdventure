<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class PublicHoliday
 * @package Divante\Bundle\AdventureBundle\Entity
 * @ORM\Entity(repositoryClass="Divante\Bundle\AdventureBundle\Entity\Repository\PublicHolidayRepository")
 * @ORM\Table(name="public_holiday")
 */
class PublicHoliday
{
    use Id, Timestampable;

    // Christian calculated holidays
    public const TYPE_CHRISTIAN_GOOD_FRIDAY = 0x01; // TYPE_EASTER - 2 days
    public const TYPE_CHRISTIAN_EASTER = 0x02;
    public const TYPE_CHRISTIAN_EASTER_MONDAY = 0x03; // TYPE_EASTER + 1 day
    public const TYPE_CHRISTIAN_ASCENSION_DAY = 0x04; // TYPE_EASTER + 39 days
    public const TYPE_CHRISTIAN_PENTECOST = 0x05; // TYPE_EASTER + 49 days
    public const TYPE_CHRISTIAN_PENTECOST_MONDAY = 0x06; // TYPE_EASTER + 50 days
    public const TYPE_CHRISTIAN_CORPUS_CHRISTI = 0x07; // TYPE_EASTER + 60 days

    // US calculated holidays
    public const TYPE_US_MARTIN_LUTHER_KING_JR_BIRTHDAY = 0x10; // Third Monday of January (15.01 - 21.01)
    public const TYPE_US_GEORGE_WASHINGTON_BIRTHDAY = 0x11; // Third Monday of February (15.02 - 21.02)
    public const TYPE_US_MEMORIAL_DAY = 0x12; // Last Monday in May (25.05 - 31.05)
    public const TYPE_US_LABOR_DAY = 0x13; // First Monday in September (01.09 - 7.09)
    public const TYPE_US_COLUMBUS_DAY = 0x14; // Second Monday in October (08.10 - 14.10)
    public const TYPE_US_THANKSGIVING_DAY = 0x15; // Fourth Thursday in November (22.11 - 28.11)


    /** @ORM\Column(name="date", type="date", nullable=true) */
    private ?DateTime $date = null;
    /** @ORM\Column(name="calculation_type", type="integer", nullable=true) */
    private ?int $calculationType = null;
    /** @ORM\Column(name="name", type="string") */
    private string $name = '';
    /** @ORM\Column(name="repeating", type="boolean") */
    private bool $repeating = false;
    /** @ORM\Column(name="enabled", type="boolean") */
    private bool $enabled = false;

    public function setDate(?DateTime $date): PublicHoliday
    {
        $this->date = $date;
        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setCalculationType(?int $calculationType): PublicHoliday
    {
        $this->calculationType = $calculationType;
        return $this;
    }

    public function getCalculationType(): ?int
    {
        return $this->calculationType;
    }

    public function setName(string $name): PublicHoliday
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setRepeating(bool $repeating): PublicHoliday
    {
        $this->repeating = $repeating;
        return $this;
    }

    public function isRepeating(): bool
    {
        return $this->repeating;
    }

    public function setEnabled(bool $enabled): PublicHoliday
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
