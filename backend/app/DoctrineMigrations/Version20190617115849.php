<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190617115849 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employee ADD date_of_birth VARCHAR(10) DEFAULT NULL, CHANGE pin_locked pin_locked TINYINT(1) NOT NULL, CHANGE fail_count fail_count INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employee DROP date_of_birth, CHANGE pin_locked pin_locked TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE fail_count fail_count INT DEFAULT 0 NOT NULL');
    }
}
