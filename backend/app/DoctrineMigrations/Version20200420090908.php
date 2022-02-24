<?php

namespace Adventure\Migrations;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200420090908 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $avazaSynced = LeaveRequestDay::AVAZA_STATUS_SYNCED;
        $avazaNotSynced = LeaveRequestDay::AVAZA_STATUS_NOT_SYNCED;
        $resigned = LeaveRequestDay::DAY_STATUS_RESIGNED;
        $cancelled = LeaveRequestDay::DAY_STATUS_CANCELED;

        $this->addSql('ALTER TABLE employee ADD avaza_id VARCHAR(16) DEFAULT NULL');
        $this->addSql(<<<SQL
            ALTER TABLE leave_request_day
                ADD avaza_sync_status INT DEFAULT $avazaSynced NOT NULL,
                ADD avaza_id VARCHAR(16) DEFAULT NULL
        SQL);
        $this->addSql(<<<SQL
            UPDATE leave_request_day
                SET avaza_sync_status = $avazaNotSynced
                WHERE status = $resigned OR status = $cancelled
        SQL);
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE employee DROP avaza_id');
        $this->addSql('ALTER TABLE leave_request_day DROP avaza_sync_status, DROP avaza_id');
    }
}
