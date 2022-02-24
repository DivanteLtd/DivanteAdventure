<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190417091257 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE fos_user DROP INDEX FK_fos_user_employee, ADD UNIQUE INDEX UNIQ_957A64798C03F15C (employee_id)');
        $this->addSql('ALTER TABLE fos_user DROP FOREIGN KEY FK_fos_user_employee');
        $this->addSql('ALTER TABLE fos_user ADD CONSTRAINT FK_957A64798C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE employee DROP manager');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE fos_user DROP INDEX UNIQ_957A64798C03F15C, ADD INDEX FK_fos_user_employee (employee_id)');
        $this->addSql('ALTER TABLE fos_user DROP FOREIGN KEY FK_957A64798C03F15C');
        $this->addSql('ALTER TABLE fos_user ADD CONSTRAINT FK_fos_user_employee FOREIGN KEY (employee_id) REFERENCES employee (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee ADD manager INT NOT NULL');
    }
}
