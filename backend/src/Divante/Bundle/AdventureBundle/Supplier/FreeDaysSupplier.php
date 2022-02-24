<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 17.01.19
 * Time: 08:41
 */

namespace Divante\Bundle\AdventureBundle\Supplier;

use DateInterval;
use DateTime;
use Divante\Bundle\AdventureBundle\Entity\PublicHoliday;
use Divante\Bundle\AdventureBundle\Entity\Repository\PublicHolidayRepository;
use Doctrine\ORM\EntityManagerInterface;

class FreeDaysSupplier
{
    private EntityManagerInterface $em;
    /**
     * Key - year
     * value - array of free dates in YYYY-MM-DD format
     * @var array<int,array<string>>
     */
    private static $cache = [];

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param int $startYear
     * @param int $endYear
     * @return string[]
     */
    public function getFreeDays(int $startYear, int $endYear) : array
    {
        /** @var PublicHolidayRepository $repo */
        $repo = $this->em->getRepository(PublicHoliday::class);
        $entries = $repo->getDaysBetweenYears($startYear, $endYear);
        $dates = [];
        for ($year = $startYear; $year <= $endYear; $year++) {
            $resultForYear = $this->getFreeDaysForYear($year, $entries);
            $dates = [ ...$dates, ...$resultForYear ];
        }
        return $dates;
    }

    public function isFreeDay(DateTime $dateTime): bool
    {
        $year = (int)$dateTime->format('Y');
        $freeDays = $this->getFreeDays($year, $year);
        return in_array($dateTime->format('Y-m-d'), $freeDays);
    }

    /**
     * @param int $year
     * @param PublicHoliday[] $entries
     * @return string[]
     */
    private function getFreeDaysForYear(int $year, array $entries): array
    {
        if (array_key_exists($year, self::$cache)) {
            return self::$cache[$year];
        }
        $resultForYear = array_map(
            function (PublicHoliday $h) use ($year) {
                return $this->mapEntryToResult($h, $year);
            },
            $entries
        );
        self::$cache[$year] = $resultForYear;
        return $resultForYear;
    }

    private function mapEntryToResult(PublicHoliday $holiday, int $year): string
    {
        return $this->getDate($holiday, $year)->format('Y-m-d');
    }

    public function getDate(PublicHoliday $holiday, int $year): DateTime
    {
        if (!is_null($holiday->getDate())) {
            $baseDate = $holiday->getDate();
            if ($holiday->isRepeating()) {
                $baseDate->setDate($year, (int)$baseDate->format('m'), (int)$baseDate->format('d'));
            }
            return $baseDate;
        }
        switch ($holiday->getCalculationType()) {
            case PublicHoliday::TYPE_CHRISTIAN_GOOD_FRIDAY:
                return $this->getEaster($year)->sub(DateInterval::createFromDateString("2 days"));
            case PublicHoliday::TYPE_CHRISTIAN_EASTER:
                return $this->getEaster($year);
            case PublicHoliday::TYPE_CHRISTIAN_EASTER_MONDAY:
                return $this->getEaster($year)->add(DateInterval::createFromDateString("1 day"));
            case PublicHoliday::TYPE_CHRISTIAN_ASCENSION_DAY:
                return $this->getEaster($year)->add(DateInterval::createFromDateString("39 days"));
            case PublicHoliday::TYPE_CHRISTIAN_PENTECOST:
                return $this->getEaster($year)->add(DateInterval::createFromDateString("49 days"));
            case PublicHoliday::TYPE_CHRISTIAN_PENTECOST_MONDAY:
                return $this->getEaster($year)->add(DateInterval::createFromDateString("50 days"));
            case PublicHoliday::TYPE_CHRISTIAN_CORPUS_CHRISTI:
                return $this->getEaster($year)->add(DateInterval::createFromDateString("60 days"));
            case PublicHoliday::TYPE_US_MARTIN_LUTHER_KING_JR_BIRTHDAY:
                return new DateTime("third monday of january $year");
            case PublicHoliday::TYPE_US_GEORGE_WASHINGTON_BIRTHDAY:
                return new DateTime("third monday of february $year");
            case PublicHoliday::TYPE_US_MEMORIAL_DAY:
                return new DateTime("last monday of may $year");
            case PublicHoliday::TYPE_US_LABOR_DAY:
                return new DateTime("third monday of september $year");
            case PublicHoliday::TYPE_US_COLUMBUS_DAY:
                return new DateTime("second monday of october $year");
            case PublicHoliday::TYPE_US_THANKSGIVING_DAY:
                return new DateTime("fourth thursday of november $year");
            default:
                return new DateTime();
        }
    }

    /**
     * Calculates date of Easter in given year, using Meeus/Jones/Butcher method.
     * @param int $year
     * @return DateTime
     */
    private function getEaster(int $year) : DateTime
    {
        $a = $year % 19;
        $b = floor($year / 100);
        $c = $year % 100;
        $d = floor($b / 4);
        $e = $b % 4;
        $f = floor(($b + 8) / 25);
        $g = floor(($b - $f + 1) / 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = floor($c / 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = floor(($a + 11 * $h + 22 * $l) / 451);
        $p = ($h + $l - 7 * $m + 114) % 31;

        $day = $p + 1;
        $month = (int)floor(($h + $l - 7 * $m + 114) / 31);
        return (new DateTime())->setDate($year, $month, $day);
    }
}
