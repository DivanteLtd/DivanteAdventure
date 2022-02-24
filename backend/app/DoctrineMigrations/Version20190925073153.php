<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190925073153 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE employee_toggl_access (id INT AUTO_INCREMENT NOT NULL, employee_id INT NOT NULL, toggl_project_id INT NOT NULL, pairing_id VARCHAR(150) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_5FCB60638C03F15C (employee_id), INDEX IDX_5FCB60635833B4DB (toggl_project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE employee_toggl_access ADD CONSTRAINT FK_5FCB60638C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE employee_toggl_access ADD CONSTRAINT FK_5FCB60635833B4DB FOREIGN KEY (toggl_project_id) REFERENCES toggl_project (id)');
        $this->addSql('ALTER TABLE employee_project DROP toggl_pairing_ids');
        $this->addSql('ALTER TABLE integration_queue ADD request_data LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE employee_toggl_access');
        $this->addSql('ALTER TABLE employee_project ADD toggl_pairing_ids VARCHAR(150) DEFAULT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE integration_queue DROP request_data');
    }
}
