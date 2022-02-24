<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210406122422 extends AbstractMigration
{

    public function up(Schema $schema) : void
    {
        $data = array_map('str_getcsv', file(getcwd() . '/app/DoctrineMigrations/data/employee.csv'));
        array_shift($data);
        foreach ($data as $row) {
            $fullName = $row[0];
            $this->write($fullName);
            $employee = $this->connection->fetchAll("SELECT id, name, last_name, email FROM employee WHERE CONCAT(last_name, ' ', name) = :full_name", ['full_name' => $fullName]);
            if (empty($employee)) {
                continue;
            }
            $this->connection->update('employee', ['finance_code' => $row[2]], ['id' => $employee[0]['id']]);
        }

    }

    public function down(Schema $schema) : void
    {
        $this->connection->update('employee', ['finance_code' => null], []);

    }
}
