<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190713091039 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE employee_end_cooperation (id INT AUTO_INCREMENT NOT NULL, employee_id INT DEFAULT NULL, next_company VARCHAR(254) DEFAULT NULL, who_ended_cooperation VARCHAR(150) DEFAULT NULL, exit_interview TINYINT(1) DEFAULT NULL, checklist TINYINT(1) DEFAULT NULL, comment VARCHAR(500) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_2782FE308C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE employee_end_cooperation ADD CONSTRAINT FK_2782FE308C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');

    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE employee_end_cooperation');

    }
}
