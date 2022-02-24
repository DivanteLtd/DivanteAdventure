<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190128071759 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE employee_daysoff_period CHANGE repeating repeating TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE employee_sick_leave_period CHANGE repeating repeating TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE employee_daysoff_request DROP FOREIGN KEY FK_employee_daysoff_request_employee');
        $this->addSql('DROP INDEX FK_employee_daysoff_request_employee ON employee_daysoff_request');
        $this->addSql('ALTER TABLE employee_daysoff_request DROP employee_id');
        $this->addSql('ALTER TABLE employee_daysoff_request_day DROP FOREIGN KEY FK_employee_daysoff_request_day_employee');
        $this->addSql('ALTER TABLE employee_daysoff_request_day DROP FOREIGN KEY FK_employee_daysoff_request_day_employee_daysoff_period');
        $this->addSql('ALTER TABLE employee_daysoff_request_day DROP FOREIGN KEY FK_employee_daysoff_request_day_employee_2');
        $this->addSql('DROP INDEX FK_employee_daysoff_request_day_employee ON employee_daysoff_request_day');
        $this->addSql('DROP INDEX FK_employee_daysoff_request_day_employee_daysoff_period ON employee_daysoff_request_day');
        $this->addSql('DROP INDEX FK_employee_daysoff_request_day_employee_2 ON employee_daysoff_request_day');
        $this->addSql('ALTER TABLE employee_daysoff_request_day DROP period_id, DROP employee_id, DROP manager_id');
        $this->addSql('ALTER TABLE employee_sick_leave_request DROP FOREIGN KEY FK_95A9364D8C03F15C');
        $this->addSql('DROP INDEX IDX_95A9364D8C03F15C ON employee_sick_leave_request');
        $this->addSql('ALTER TABLE employee_sick_leave_request DROP employee_id');
        $this->addSql('ALTER TABLE employee_sick_leave_request_day DROP FOREIGN KEY FK_1EF349C3783E3463');
        $this->addSql('ALTER TABLE employee_sick_leave_request_day DROP FOREIGN KEY FK_1EF349C38C03F15C');
        $this->addSql('ALTER TABLE employee_sick_leave_request_day DROP FOREIGN KEY FK_1EF349C3EC8B7ADE');
        $this->addSql('DROP INDEX IDX_1EF349C38C03F15C ON employee_sick_leave_request_day');
        $this->addSql('DROP INDEX IDX_1EF349C3783E3463 ON employee_sick_leave_request_day');
        $this->addSql('DROP INDEX IDX_1EF349C3EC8B7ADE ON employee_sick_leave_request_day');
        $this->addSql('ALTER TABLE employee_sick_leave_request_day DROP period_id, DROP employee_id, DROP manager_id');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE employee_daysoff_period CHANGE repeating repeating INT NOT NULL');
        $this->addSql('ALTER TABLE employee_sick_leave_period CHANGE repeating repeating INT NOT NULL');
        $this->addSql('ALTER TABLE employee_daysoff_request ADD employee_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employee_daysoff_request ADD CONSTRAINT FK_2AFE83988C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('CREATE INDEX FK_employee_daysoff_request_employee ON employee_daysoff_request (employee_id)');
        $this->addSql('ALTER TABLE employee_daysoff_request_day ADD period_id INT DEFAULT NULL, ADD employee_id INT DEFAULT NULL, ADD manager_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employee_daysoff_request_day ADD CONSTRAINT FK_62C8591D783E3463 FOREIGN KEY (manager_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE employee_daysoff_request_day ADD CONSTRAINT FK_62C8591D8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE employee_daysoff_request_day ADD CONSTRAINT FK_62C8591DEC8B7ADE FOREIGN KEY (period_id) REFERENCES employee_daysoff_period (id)');
        $this->addSql('CREATE INDEX FK_employee_daysoff_request_day_employee ON employee_daysoff_request_day (employee_id)');
        $this->addSql('CREATE INDEX FK_employee_daysoff_request_day_employee_daysoff_period ON employee_daysoff_request_day (period_id)');
        $this->addSql('CREATE INDEX IDX_62C8591D783E3463 ON employee_daysoff_request_day (manager_id)');
        $this->addSql('ALTER TABLE employee_sick_leave_request ADD employee_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employee_sick_leave_request ADD CONSTRAINT FK_95A9364D8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('CREATE INDEX IDX_95A9364D8C03F15C ON employee_sick_leave_request (employee_id)');
        $this->addSql('ALTER TABLE employee_sick_leave_request_day ADD period_id INT DEFAULT NULL, ADD employee_id INT DEFAULT NULL, ADD manager_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employee_sick_leave_request_day ADD CONSTRAINT FK_1EF349C3783E3463 FOREIGN KEY (manager_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE employee_sick_leave_request_day ADD CONSTRAINT FK_1EF349C38C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE employee_sick_leave_request_day ADD CONSTRAINT FK_1EF349C3EC8B7ADE FOREIGN KEY (period_id) REFERENCES employee_sick_leave_period (id)');
        $this->addSql('CREATE INDEX IDX_1EF349C38C03F15C ON employee_sick_leave_request_day (employee_id)');
        $this->addSql('CREATE INDEX IDX_1EF349C3783E3463 ON employee_sick_leave_request_day (manager_id)');
        $this->addSql('CREATE INDEX IDX_1EF349C3EC8B7ADE ON employee_sick_leave_request_day (period_id)');
    }
}
