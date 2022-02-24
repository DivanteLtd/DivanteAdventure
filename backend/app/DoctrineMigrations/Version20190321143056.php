<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190321143056 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employee ADD gender INT DEFAULT NULL, ADD emergency_first_name VARCHAR(255) DEFAULT NULL, ADD emergency_last_name VARCHAR(255) DEFAULT NULL, ADD emergency_address VARCHAR(255) DEFAULT NULL, ADD emergency_phone VARCHAR(255) DEFAULT NULL, CHANGE job_time_value job_time_value NUMERIC(10, 0) NOT NULL');

    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
