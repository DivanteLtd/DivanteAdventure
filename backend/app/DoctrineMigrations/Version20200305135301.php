<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200305135301 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DELETE ea1 FROM employee_agreement ea1 INNER JOIN employee_agreement ea2 ON ea1.id < ea2.id AND ea1.employee_id = ea2.employee_id and ea1.agreement_id = ea2.agreement_id;');
        $this->addSql('ALTER TABLE employee_agreement DROP INDEX FK_employee_agreement_employee, ADD UNIQUE INDEX employee_id_agreement_id (employee_id, agreement_id)');

    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employee_agreement DROP INDEX employee_id_agreement_id, ADD INDEX FK_employee_agreement_employee (employee_id, agreement_id)');

    }
}
