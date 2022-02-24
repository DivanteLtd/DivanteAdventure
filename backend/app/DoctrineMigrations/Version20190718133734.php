<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20190718133734 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE employee_end_cooperation ADD name VARCHAR(254) DEFAULT NULL, ADD lastname VARCHAR(254) DEFAULT NULL, ADD position VARCHAR(254) DEFAULT NULL, ADD dismiss_date VARCHAR(254) DEFAULT NULL');

    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE employee_end_cooperation DROP name, DROP lastname, DROP position, DROP dismiss_date');

    }
}
