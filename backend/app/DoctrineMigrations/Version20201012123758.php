<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201012123758 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE potential_employee ADD contract_type VARCHAR(30) NOT NULL, DROP contact_type, CHANGE language language VARCHAR(2) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE potential_employee ADD contact_type VARCHAR(10) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, DROP contract_type, CHANGE language language VARCHAR(2) CHARACTER SET utf8 DEFAULT \'en\' NOT NULL COLLATE `utf8_unicode_ci`');
    }
}
