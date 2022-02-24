<?php

namespace Adventure\Migrations;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190212114636 extends AbstractMigration
{
    // Old values for Sick Leave day type
    private const DAY_TYPE_SICK_LEAVE_PAID_OLD = 0;
    private const DAY_TYPE_SICK_LEAVE_UNPAID_OLD = 1;

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Updated type of sick leave days
        $this->addSql("UPDATE employee_sick_leave_request_day SET type = '".LeaveRequestDay::DAY_TYPE_SICK_LEAVE_PAID."' WHERE type = '".self::DAY_TYPE_SICK_LEAVE_PAID_OLD."'");
        $this->addSql("UPDATE employee_sick_leave_request_day SET type = '".LeaveRequestDay::DAY_TYPE_SICK_LEAVE_UNPAID."' WHERE type = '".self::DAY_TYPE_SICK_LEAVE_UNPAID_OLD."'");

        // Creating common table for leave request days
        $this->addSql("CREATE TABLE leave_request_day (id INT AUTO_INCREMENT PRIMARY KEY, request_id INT NULL, type INT NOT NULL, status INT NOT NULL, date INT NOT NULL, CONSTRAINT FK_leave_request_day_leave_request FOREIGN KEY (request_id) REFERENCES leave_request (id) ON DELETE CASCADE)");
        $this->addSql("INSERT INTO leave_request_day(request_id, type, status, date) SELECT sld.request_id, sld.type, sld.status, sld.date FROM employee_sick_leave_request_day AS sld UNION SELECT drd.request_id, drd.type, drd.status, drd.date FROM employee_daysoff_request_day AS drd");

        // dropping tables
        $this->addSql("DROP TABLE employee_daysoff_request_day");
        $this->addSql("DROP TABLE employee_sick_leave_request_day");
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
