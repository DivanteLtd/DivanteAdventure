<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190117065839 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->upByTable("employee_sick_leave_request_day");
        $this->upByTable("employee_daysoff_request_day");
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->downByTable("employee_sick_leave_request_day");
        $this->downByTable("employee_daysoff_request_day");
    }

    private function upByTable(string $name) : void
    {
        $this->addSql("ALTER TABLE `$name` ADD `date_timestamp` INT NULL");
        $this->addSql("UPDATE `$name` SET `date_timestamp` = UNIX_TIMESTAMP(`date`)");
        $this->addSql("ALTER TABLE `$name` MODIFY `date_timestamp` INT NOT NULL");
        $this->addSql("ALTER TABLE `$name` DROP COLUMN `date`");
        $this->addSql("ALTER TABLE `$name` CHANGE `date_timestamp` `date` INT NOT NULL");
    }

    private function downByTable(string $name) : void
    {
        $this->addSql("ALTER TABLE `$name` add `date_date` DATE NULL");
        $this->addSql("UPDATE `$name` SET `date_date` = FROM_UNIXTIME(`date`)");
        $this->addSql("ALTER TABLE `$name` MODIFY `date_date` DATE NOT NULL");
        $this->addSql("ALTER TABLE `$name` DROP COLUMN `date`");
        $this->addSql("ALTER TABLE `$name` CHANGE `date_date` `date` DATE NOT NULL");
    }
}
