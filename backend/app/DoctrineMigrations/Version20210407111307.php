<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210407111307 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $data = array_map('str_getcsv', file(getcwd() . '/app/DoctrineMigrations/data/DIGIT-95.csv'));
        array_shift($data);
        foreach ($data as $row) {
            $this->connection->update('employee', ['employee_code' => $row[2]], ['email' => $row[1]]);
        }

    }

    public function down(Schema $schema) : void
    {
        $this->connection->update('employee', ['employee_code' => null], []);
    }
}
