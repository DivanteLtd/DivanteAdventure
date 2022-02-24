<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210422075155 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $data = array_map('str_getcsv', file(getcwd() . '/app/DoctrineMigrations/data/DIGIT-105.csv'));
        array_shift($data);
        foreach ($data as $row) {
            $isKUP = 0;
            $isStudent = 0;
            if ($row[1] === 'TAK') {
                $isKUP = 1;
            }
            if ($row[2] == 'TAK') {
                $isStudent = 1;
            }
            $qb = $this->connection->createQueryBuilder();
            $qb->update('employee')
                ->set('student', $isStudent)
                ->set('tax_deductible_costs', $isKUP)
                ->where("email like '".explode('@',$row[3])[0]."%'")
                ->execute();

        }
        $data = array_map('str_getcsv', file(getcwd() . '/app/DoctrineMigrations/data/DIGIT-105_1.csv'));
        array_shift($data);
        foreach ($data as $row) {
            $qb = $this->connection->createQueryBuilder();
            $qb->update('employee')
                ->set('finance_code', "'".$row[3]."'")
                ->where("email like '".explode('@',$row[4])[0]."%'")
                ->execute();
        }
    }

    public function down(Schema $schema) : void
    {

    }
}
