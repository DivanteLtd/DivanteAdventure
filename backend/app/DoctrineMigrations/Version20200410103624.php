<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200410103624 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('update project set visibility = 0 WHERE visibility is null');
        $this->addSql('ALTER TABLE project CHANGE visibility archived TINYINT(1) DEFAULT 0 NOT NULL');

    }

    public function down(Schema $schema) : void
    {

        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE project CHANGE archived visibility int NULL');
    }
}
