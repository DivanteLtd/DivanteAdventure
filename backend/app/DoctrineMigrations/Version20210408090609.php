<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210408090609 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employee ADD student TINYINT(1) NULL, ADD tax_deductible_costs INT DEFAULT 0 NULL, ADD work_street VARCHAR(255) DEFAULT NULL, ADD work_city VARCHAR(255) DEFAULT NULL, ADD work_postal_code VARCHAR(255) DEFAULT NULL, ADD work_country VARCHAR(255) DEFAULT NULL');

    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employee  DROP student, DROP tax_deductible_costs, DROP work_street, DROP work_city, DROP work_postal_code, DROP work_country');

    }
}
