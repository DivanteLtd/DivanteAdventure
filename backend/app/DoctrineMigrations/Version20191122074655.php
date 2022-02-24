<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191122074655 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE employee ADD last_slack_message_sent DATETIME NULL');
        $this->addSql('ALTER TABLE project ADD last_slack_message_sent DATETIME NULL');
        $this->addSql('ALTER TABLE tribe ADD last_slack_message_sent DATETIME NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql('ALTER TABLE employee DROP last_slack_message_sent');
        $this->addSql('ALTER TABLE project DROP last_slack_message_sent');
        $this->addSql('ALTER TABLE tribe DROP last_slack_message_sent');
    }
}
