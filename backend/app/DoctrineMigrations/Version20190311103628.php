<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190311103628 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employee_project ADD date_from LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD date_to LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', DROP started_at, DROP ended_at, CHANGE employee_id employee_id INT DEFAULT NULL, CHANGE project_id project_id INT DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employee_project ADD started_at DATE DEFAULT NULL, ADD ended_at DATE DEFAULT NULL, DROP date_from, DROP date_to, CHANGE project_id project_id INT NOT NULL, CHANGE employee_id employee_id INT NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
