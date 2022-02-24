<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190502121820 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE leave_request_day ADD period_id INT DEFAULT NULL, ADD employee_id INT DEFAULT NULL');
        $this->addSql('UPDATE leave_request_day AS lrd JOIN leave_request lr ON lrd.request_id = lr.id JOIN leave_period lp ON lr.period_id = lp.id SET lrd.employee_id = lp.employee_id, lrd.period_id = lp.id');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE leave_request_day DROP period_id, DROP employee_id');
    }
}
