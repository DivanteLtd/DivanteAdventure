<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200211120442 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE hardware_agreement ADD `generated` TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE hardware_agreement CHANGE serialnumber serial_number VARCHAR(100) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE hardware_agreement DROP `generated`');
        $this->addSql('ALTER TABLE hardware_agreement CHANGE serial_number serialNumber VARCHAR(100) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
