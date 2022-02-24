<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190314065836 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO sick_leave_request_attachment(sick_leave_request_id, sick_leave_attachment_id) SELECT request_id, id FROM sick_leave_attachment");
        $this->addSql("ALTER TABLE sick_leave_attachment DROP FOREIGN KEY FK_sick_leave_attachment3_request");
        $this->addSql("ALTER TABLE sick_leave_attachment DROP COLUMN request_id");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("ALTER TABLE sick_leave_attachment ADD COLUMN request_id INTEGER NULL");
        $this->addSql("ALTER TABLE sick_leave_attachment ADD CONSTRAINT FK_sick_leave_attachment3_request FOREIGN KEY (request_id) REFERENCES leave_request (id) ON UPDATE CASCADE ON DELETE CASCADE");
        $this->addSql("UPDATE sick_leave_attachment AS sla JOIN sick_leave_request_attachment AS slra ON sla.id = slra.sick_leave_attachment_id SET sla.request_id = slra.sick_leave_request_id");
        $this->addSql("TRUNCATE TABLE sick_leave_request_attachment");
    }
}
