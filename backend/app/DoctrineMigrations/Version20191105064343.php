<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191105064343 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql('ALTER TABLE project ADD slack_id LONGTEXT DEFAULT NULL, ADD slack_status SMALLINT NOT NULL, ADD slack_access_token LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE tribe ADD slack_id LONGTEXT DEFAULT NULL, ADD slack_status SMALLINT NOT NULL, ADD slack_access_token LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql('ALTER TABLE project DROP slack_id, DROP slack_status, DROP slack_access_token');
        $this->addSql('ALTER TABLE tribe DROP slack_id, DROP slack_status, DROP slack_access_token');
    }
}
