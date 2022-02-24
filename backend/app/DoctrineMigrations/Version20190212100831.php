<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 12.02.19
 * Time: 10:08
 */

namespace Adventure\Migrations;


use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190212100831 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Creating leave_request table
        $this->addSql("CREATE TABLE leave_request(id INT AUTO_INCREMENT PRIMARY KEY, period_id INT NULL, manager_id INT NULL, comment TEXT NULL, accepted_at DATETIME NULL, status INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, resignation INT NOT NULL, hidden TINYINT(1) DEFAULT 0 NOT NULL, CONSTRAINT FK_employee_leave_request_employee_1 FOREIGN KEY (manager_id) REFERENCES employee (id) ON DELETE CASCADE, CONSTRAINT FK_employee_leave_request_period_1 FOREIGN KEY (period_id) REFERENCES leave_period (id) ON DELETE CASCADE) COLLATE = utf8_unicode_ci");
        $this->addSql("CREATE INDEX IDX_leave_request_manager_id ON leave_request (manager_id)");
        $this->addSql("CREATE INDEX IDX_leave_request_period_id ON leave_request (period_id)");
        $this->addSql("INSERT INTO leave_request (period_id, manager_id, comment, accepted_at, status, created_at, updated_at, resignation, hidden) SELECT eslr.period_id, eslr.manager_id, eslr.comment COLLATE utf8_general_ci, eslr.accepted_at, eslr.status, eslr.created_at, eslr.updated_at, eslr.resignation, eslr.hidden FROM employee_sick_leave_request AS eslr UNION SELECT edr.period_id, edr.manager_id, edr.comment COLLATE utf8_general_ci, edr.accepted_at, edr.status, edr.created_at, edr.updated_at, edr.resignation, 0 AS `hidden` FROM employee_daysoff_request AS edr");

        // Creating temporary table for daysoff_request_days
        $this->addSql("CREATE TABLE tmp_1 (id INT AUTO_INCREMENT PRIMARY KEY, request_id INT DEFAULT 0 NOT NULL, type TINYINT DEFAULT 0 NOT NULL, status TINYINT DEFAULT 0 NOT NULL, date INT NOT NULL, CONSTRAINT FK_tmp1_request FOREIGN KEY (request_id) REFERENCES leave_request (id) ON UPDATE CASCADE ON DELETE CASCADE) CHARSET = utf8");
        $this->addSql("INSERT INTO tmp_1(request_id, type, status, date) SELECT lr.id, edrd.type, edrd.status, edrd.date FROM employee_daysoff_request_day AS edrd JOIN employee_daysoff_request AS edr on edrd.request_id = edr.id JOIN leave_request AS lr ON lr.created_at = edr.created_at AND lr.updated_at = edr.updated_at AND lr.manager_id = edr.manager_id AND lr.status = edr.status AND lr.period_id = edr.period_id");

        // Removing old daysoff_request_days table and replacing it with new one
        $this->addSql("DROP TABLE employee_daysoff_request_day");
        $this->addSql("RENAME TABLE tmp_1 TO employee_daysoff_request_day");

        // Creating temporary table for sick_leave_request_days
        $this->addSql("CREATE TABLE tmp_1 (id INT AUTO_INCREMENT PRIMARY KEY, request_id INT NULL, type INT NOT NULL, status INT NOT NULL, date INT NOT NULL, CONSTRAINT FK_tmp_1_request FOREIGN KEY (request_id) REFERENCES leave_request (id) ON DELETE CASCADE)");
        $this->addSql("INSERT INTO tmp_1(request_id, type, status, date) SELECT lr.id, eslrd.type, eslrd.status, eslrd.date FROM employee_sick_leave_request_day AS eslrd JOIN employee_sick_leave_request AS eslr on eslrd.request_id = eslr.id JOIN leave_request AS lr ON lr.created_at = eslr.created_at AND lr.updated_at = eslr.updated_at AND lr.manager_id = eslr.manager_id AND lr.status = eslr.status AND lr.period_id = eslr.period_id");

        // Creating temporary table for sick_leave_attachments
        $this->addSql("CREATE TABLE tmp_2 (id INT AUTO_INCREMENT PRIMARY KEY, employee_id INT NULL, path VARCHAR(100) NOT NULL UNIQUE, name VARCHAR(100) NOT NULL, request_id INT NULL, CONSTRAINT FK_sick_leave_attachment3_request FOREIGN KEY (request_id) REFERENCES leave_request (id) ON UPDATE CASCADE ON DELETE CASCADE, CONSTRAINT fk_sick_leave_attachment3_employee FOREIGN KEY (employee_id) REFERENCES employee (id)) COLLATE = utf8_unicode_ci");
        $this->addSql("CREATE INDEX fk_sick_leave_attachment3_employee ON tmp_2 (employee_id)");
        $this->addSql("INSERT INTO tmp_2 (employee_id, path, name, request_id) SELECT sla.employee_id, sla.path, sla.name, lr.id FROM sick_leave_attachment AS sla JOIN employee_sick_leave_request AS eslr ON sla.request_id = eslr.id JOIN leave_request AS lr ON lr.created_at = eslr.created_at AND lr.updated_at = eslr.updated_at AND lr.manager_id = eslr.manager_id AND lr.status = eslr.status AND lr.period_id = eslr.period_id");
        $this->addSql("CREATE TABLE tmp_3 (sick_leave_request_id INT NOT NULL, sick_leave_attachment_id INT NOT NULL, PRIMARY KEY (sick_leave_request_id, sick_leave_attachment_id), CONSTRAINT FK_sick_leave_request_attachment3_request FOREIGN KEY (sick_leave_request_id) REFERENCES leave_request (id) ON DELETE CASCADE, CONSTRAINT FK_sick_leave_request_attachment3_attachment FOREIGN KEY (sick_leave_attachment_id) REFERENCES tmp_2 (id) ON DELETE CASCADE) COLLATE = utf8_unicode_ci");

        // Removing old sick_leave tables and replacing them with new ones
        $this->addSql("DROP TABLE employee_sick_leave_request_day");
        $this->addSql("RENAME TABLE tmp_1 TO employee_sick_leave_request_day");
        $this->addSql("DROP TABLE sick_leave_request_attachment");
        $this->addSql("RENAME TABLE tmp_3 TO sick_leave_request_attachment");
        $this->addSql("DROP TABLE sick_leave_attachment");
        $this->addSql("RENAME TABLE tmp_2 TO sick_leave_attachment");
        $this->addSql("DROP TABLE employee_sick_leave_request");
        $this->addSql("DROP TABLE employee_daysoff_request");
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}