<?php

namespace Tests;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Symfony\Component\HttpFoundation\Request;

class AdventureTestUtils
{
    private function __construct()
    {}

    private const FEMALE_NAMES = [
        'Zuzanna' => 'z',
        'Julia' => 'j',
        'Maja' => 'm',
        'Zofia' => 'z',
        'Hanna' => 'h',
        'Lena' => 'l',
        'Alicja' => 'a',
        'Maria' => 'm',
        'Amelia' => 'a',
        'Oliwia' => 'o',
    ];

    private const MALE_NAMES = [
        'Antoni' => 'a',
        'Jakub' => 'j',
        'Jan' => 'j',
        'Szymon' => 's',
        'Aleksander' => 'a',
        'Franciszek' => 'f',
        'Filip' => 'f',
        'Mikołaj' => 'm',
        'Wojciech' => 'w',
        'Kacper' => 'k',
    ];

    private const LASTNAMES = [
        'Nowak' => 'nowak',
        'Wójcik' => 'wojcik',
        'Kowalczyk' => 'kowalczyk',
        'Woźniak' => 'wozniak',
        'Mazur' => 'mazur',
        'Krawczyk' => 'krawczyk',
        'Kaczmarek' => 'kaczmarek',
        'Zając' => 'zajac',
        'Król' => 'krol',
        'Wieczorek' => 'wieczorek'
    ];

    public static function generateEmployee() : Employee
    {
        $gender = rand(0, 1) === 0 ? Employee::GENDER_MALE : Employee::GENDER_FEMALE;
        if ($gender === Employee::GENDER_MALE) {
            $name = array_rand(self::MALE_NAMES);
            $firstLetter = self::MALE_NAMES[$name];
        } else {
            $name = array_rand(self::FEMALE_NAMES);
            $firstLetter = self::FEMALE_NAMES[$name];
        }
        $lastName = array_rand(self::LASTNAMES);
        $emailPart = self::LASTNAMES[$lastName];
        $randomInt = rand(0, 5000);
        $timestamp = time();
        $fullLastName = sprintf('%s.%d.%d', $lastName, $randomInt, $timestamp);
        $fullEmail = sprintf('%s%s.%d.%d@example.com', $firstLetter, $emailPart, $randomInt, $timestamp);

        $employee = new Employee();
        $employee
            ->setEmail($fullEmail)
            ->setName($name)
            ->setLastName($fullLastName)
            ->setGender($gender)
            ->setCreatedAt()
            ->setUpdatedAt();
        return $employee;
    }

    public static function createRequest(array $data) : Request
    {
        return Request::create('', '', $data);
    }
}
