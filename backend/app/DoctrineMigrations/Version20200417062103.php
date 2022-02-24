<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200417062103 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE leave_request_day ADD tmp DATE DEFAULT NULL');
        $this->addSql('UPDATE leave_request_day SET tmp = STR_TO_DATE(FROM_UNIXTIME(`date`, "%d %m %Y"), "%d %m %Y")');
        $this->addSql('ALTER TABLE leave_request_day DROP date, CHANGE tmp date DATE NOT NULL');
    }

    public function down(Schema $schema) : void
    {
    }
}
