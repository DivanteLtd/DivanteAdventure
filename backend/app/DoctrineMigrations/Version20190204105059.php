<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190204105059 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE evidence (id INT AUTO_INCREMENT NOT NULL, employee_id INT DEFAULT NULL, manager_id INT DEFAULT NULL, month SMALLINT NOT NULL, year SMALLINT NOT NULL, working_hours SMALLINT NOT NULL, payed_free_hours SMALLINT NOT NULL, unpayed_free_hours SMALLINT NOT NULL, sick_leave_hours SMALLINT NOT NULL, evidence_status SMALLINT NOT NULL, overtime_status SMALLINT NOT NULL, employee_comment TEXT DEFAULT NULL, administration_comment TEXT DEFAULT NULL, INDEX IDX_C6157108C03F15C (employee_id), INDEX IDX_C615710783E3463 (manager_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evidence_overtime (id INT AUTO_INCREMENT NOT NULL, evidence_id INT NOT NULL, project_name VARCHAR(255) NOT NULL, project_code VARCHAR(255) NOT NULL, hours SMALLINT NOT NULL, percentage SMALLINT NOT NULL, time_info VARCHAR(255) NOT NULL, INDEX IDX_F14A9894B528FC11 (evidence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evidence ADD CONSTRAINT FK_C6157108C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE evidence ADD CONSTRAINT FK_C615710783E3463 FOREIGN KEY (manager_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE evidence_overtime ADD CONSTRAINT FK_F14A9894B528FC11 FOREIGN KEY (evidence_id) REFERENCES evidence (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE evidence_overtime DROP FOREIGN KEY FK_F14A9894B528FC11');
        $this->addSql('DROP TABLE evidence');
        $this->addSql('DROP TABLE evidence_overtime');
    }
}
