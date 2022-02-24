<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190717112825 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $employees = $this->connection->fetchAll('SELECT id, name, lastname, email FROM employee');
        foreach ($employees as $employee) {
            $this->connection->update('employee_agreement',
                ["name" => $employee["name"], "lastname" => $employee["lastname"], "email" => $employee["email"]],
                ["employee_id" => $employee["id"]]
            );
        }
    }

    public function down(Schema $schema) : void
    {
        $this->throwIrreversibleMigrationException('Nothing');
    }
}
