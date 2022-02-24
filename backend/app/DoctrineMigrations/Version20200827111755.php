<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20200827111755 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employee ADD postal_code VARCHAR(50) DEFAULT NULL, ADD street VARCHAR(100) DEFAULT NULL, ADD country VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE potential_employee ADD country VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employee DROP postal_code, DROP street, DROP country');
        $this->addSql('ALTER TABLE potential_employee DROP country');
    }
}
