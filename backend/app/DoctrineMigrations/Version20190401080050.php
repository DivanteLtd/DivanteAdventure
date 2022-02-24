<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190401080050 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE evidence_overtime CHANGE hours hours NUMERIC(5, 2) NOT NULL');
        $this->addSql('ALTER TABLE evidence CHANGE working_hours working_hours NUMERIC(5, 2) NOT NULL, CHANGE payed_free_hours payed_free_hours NUMERIC(5, 2) NOT NULL, CHANGE unpayed_free_hours unpayed_free_hours NUMERIC(5, 2) NOT NULL, CHANGE sick_leave_hours sick_leave_hours NUMERIC(5, 2) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE evidence CHANGE working_hours working_hours SMALLINT NOT NULL, CHANGE payed_free_hours payed_free_hours SMALLINT NOT NULL, CHANGE unpayed_free_hours unpayed_free_hours SMALLINT NOT NULL, CHANGE sick_leave_hours sick_leave_hours SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE evidence_overtime CHANGE hours hours SMALLINT NOT NULL');
    }
}
