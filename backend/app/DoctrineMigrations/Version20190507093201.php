<?php

namespace Adventure\Migrations;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190507093201 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $date = (new \DateTime())->setTime(12, 0)->getTimestamp();
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE leave_request_day ADD toggl_status SMALLINT NOT NULL DEFAULT 1, ADD toggl_id VARCHAR(255) DEFAULT NULL, ADD toggl_error VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE leave_request_day SET toggl_status = 0 WHERE date < '.$date);
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE leave_request_day DROP toggl_status, DROP toggl_id, DROP toggl_error');
    }
}
