<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191023103224 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE employee ADD slack_id LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE employee ADD slack_access_token LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE employee ADD slack_status TINYINT(1) NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE employee DROP slack_id');
        $this->addSql('ALTER TABLE employee DROP slack_status');
        $this->addSql('ALTER TABLE employee DROP slack_access_token');
    }
}
