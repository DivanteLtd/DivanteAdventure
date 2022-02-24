<?php

namespace Adventure\Migrations;

use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200228112134 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql(<<<SQL
            CREATE TABLE tmp_1 (
                id          INT AUTO_INCREMENT PRIMARY KEY,
                employee_id INT NOT NULL,
                type        INT NOT NULL,
                date        DATE NOT NULL
            ) collate = utf8_unicode_ci;
        SQL);
        /** @var array $entries */
        $entries = $this->connection->fetchAll(
            "SELECT employee_id, type, date_from, date_to FROM employee_work_location"
        );
        foreach ($entries as $entry) {
            [
                'employee_id' => $employeeId,
                'type' => $type,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ] = $entry;
            $period = new DatePeriod(
                (new DateTime($dateFrom))->setTime(12, 0),
                new DateInterval("P1D"),
                (new DateTime($dateTo))->setTime(12, 0)
            );
            /** @var DateTime[] $dates */
            $dates = iterator_to_array($period);
            $dates[] = (new DateTime($dateTo))->setTime(12, 0);
            /** @var DateTime $date */
            foreach ($dates as $date) {
                $dateFormatted = $date->format('Y-m-d');
                $sql = "INSERT INTO tmp_1(employee_id, type, date) VALUES ($employeeId, $type, '$dateFormatted')";
                $this->addSql($sql);
            }
        }
        $this->addSql("DROP TABLE employee_work_location");
        $this->addSql("ALTER TABLE tmp_1 RENAME TO employee_work_location");
    }
    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql("DROP TABLE employee_work_location");
        $this->addSql(<<<SQL
            CREATE TABLE employee_work_location (
                id          INT AUTO_INCREMENT PRIMARY KEY,
                employee_id INT NOT NULL,
                type        INT NOT NULL ,
                date_from   DATETIME NOT NULL,
                date_to     DATETIME NOT NULL
            ) collate = utf8_unicode_ci;
        SQL);
    }
}
