<?php
namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20190208080630 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Creating leave_period table and filling it with existing period, merged into one
        $this->addSql("CREATE TABLE leave_period (id INT AUTO_INCREMENT PRIMARY KEY, employee_id INT NOT NULL, date_from VARCHAR(255) NOT NULL, date_to VARCHAR(255) NOT NULL, freedays INT NOT NULL, sick_leave_days INT NOT NULL, repeating INT NOT NULL, comment_system TEXT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, CONSTRAINT fk_employee_leave_period FOREIGN KEY (employee_id) REFERENCES employee (id)) CHARSET = utf8");
        $this->addSql("CREATE INDEX FK_leave_period_employee ON leave_period (employee_id)");
        $this->addSql("INSERT INTO leave_period(date_from, date_to, freedays, sick_leave_days, repeating, comment_system, employee_id, created_at, updated_at) SELECT eslp.date_from, eslp.date_to, IFNULL(eslp.freedays, 0), IFNULL(eslp2.sick_leave_days, 0), eslp.repeating, eslp.comment_system, eslp.employee_id, eslp.created_at, eslp.updated_at FROM employee_daysoff_period AS eslp LEFT JOIN employee_sick_leave_period AS eslp2 ON eslp2.employee_id = eslp.employee_id AND eslp2.date_to COLLATE utf8_general_ci = eslp.date_to COLLATE utf8_general_ci AND eslp2.date_from COLLATE utf8_general_ci = eslp.date_from COLLATE utf8_general_ci");

        // creating temporary table for daysoff_requests
        $this->addSql("CREATE TABLE tmp_1 (id INT AUTO_INCREMENT PRIMARY KEY, period_id INT NULL, manager_id INT NULL, comment TEXT NULL, status INT NOT NULL, accepted_at DATETIME NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, resignation INT NOT NULL, CONSTRAINT FK_employee_daysoff_request2_employee_2 FOREIGN KEY (manager_id) REFERENCES employee (id) ON UPDATE CASCADE, CONSTRAINT FK_employee_daysoff_request2_leave_period FOREIGN KEY (period_id) REFERENCES leave_period (id) ON UPDATE CASCADE ON DELETE CASCADE) CHARSET = utf8");
        $this->addSql("INSERT INTO tmp_1 (period_id, manager_id, comment, status, accepted_at, created_at, updated_at, resignation) SELECT lp.id, edr.manager_id, edr.comment, edr.status, edr.accepted_at, edr.created_at, edr.updated_at, edr.resignation FROM employee_daysoff_request AS edr JOIN employee_daysoff_period AS edp ON edr.period_id = edp.id JOIN leave_period AS lp ON lp.employee_id = edp.employee_id AND lp.date_from = edp.date_from AND lp.date_to = edp.date_to");

        // creating temporary table for daysoff_request_days
        $this->addSql("CREATE TABLE tmp_2 (id INT AUTO_INCREMENT PRIMARY KEY, request_id INT DEFAULT 0 NOT NULL, type TINYINT DEFAULT 0 NOT NULL, status TINYINT DEFAULT 0 NOT NULL, date INT NOT NULL, CONSTRAINT FK_tmp2_tmp1 FOREIGN KEY (request_id) REFERENCES tmp_1 (id) ON UPDATE CASCADE ON DELETE CASCADE) CHARSET = utf8");
        $this->addSql("INSERT INTO tmp_2 (request_id, type, status, date) SELECT tmp.id AS new_request_id, edrd.type, edrd.status, edrd.date FROM employee_daysoff_request_day AS edrd JOIN employee_daysoff_request AS edr ON edrd.request_id = edr.id JOIN employee_daysoff_period AS edp ON edr.period_id = edp.id JOIN leave_period AS lp ON lp.employee_id = edp.employee_id AND lp.date_from = edp.date_from AND lp.date_to = edp.date_to JOIN tmp_1 AS tmp ON lp.id = tmp.period_id AND tmp.created_at = edr.created_at ORDER BY edrd.id");

        // Removing old daysoff tables and replacing them with new ones
        $this->addSql("DROP TABLE employee_daysoff_request_day");
        $this->addSql("RENAME TABLE tmp_2 TO employee_daysoff_request_day");
        $this->addSql("DROP TABLE employee_daysoff_request");
        $this->addSql("RENAME TABLE tmp_1 TO employee_daysoff_request");
        $this->addSql("DROP TABLE employee_daysoff_period");

        // creating temporary table for sick leave requests
        $this->addSql("CREATE TABLE tmp_1 (id INT AUTO_INCREMENT PRIMARY KEY, period_id INT NULL, manager_id INT NULL, comment TEXT NULL, accepted_at DATETIME NULL, status INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, resignation INT NOT NULL, hidden TINYINT(1) DEFAULT 0 NOT NULL, CONSTRAINT FK_employee_sick_leave_request_2_employee_1 FOREIGN KEY (manager_id) REFERENCES employee (id) ON DELETE CASCADE, CONSTRAINT FK_employee_sick_leave_request_2_period_1 FOREIGN KEY (period_id) REFERENCES leave_period (id) ON DELETE CASCADE) COLLATE = utf8_unicode_ci");
        $this->addSql("CREATE INDEX IDX_employee_sick_leave_request_2_manager_id ON tmp_1 (manager_id)");
        $this->addSql("INSERT INTO tmp_1 (period_id, manager_id, comment, accepted_at, status, created_at, updated_at, resignation, hidden) SELECT lp.id, eslr.manager_id, eslr.comment, eslr.accepted_at, eslr.status, eslr.created_at, eslr.updated_at, eslr.resignation, eslr.hidden FROM employee_sick_leave_request AS eslr JOIN employee_sick_leave_period AS eslp ON eslr.period_id = eslp.id JOIN leave_period AS lp ON lp.employee_id = eslp.employee_id AND lp.date_from COLLATE utf8_general_ci = eslp.date_from COLLATE utf8_general_ci AND lp.date_to COLLATE utf8_general_ci = eslp.date_to COLLATE utf8_general_ci");

        // creating temporary table for sick_leave_request_days
        $this->addSql("CREATE TABLE tmp_2 (id INT AUTO_INCREMENT PRIMARY KEY, request_id INT NULL, type INT NOT NULL, status INT NOT NULL, date INT NOT NULL, CONSTRAINT FK_employee_sick_leave_request_day2_request FOREIGN KEY (request_id) REFERENCES tmp_1 (id)ON DELETE CASCADE)COLLATE = utf8_unicode_ci");
        $this->addSql("INSERT INTO tmp_2 (request_id, type, status, date) SELECT tmp.id AS request_new, eslrd.type, eslrd.status, eslrd.date FROM employee_sick_leave_request_day AS eslrd JOIN employee_sick_leave_request eslr on eslrd.request_id = eslr.id JOIN employee_sick_leave_period eslp on eslr.period_id = eslp.id JOIN leave_period AS lp ON lp.employee_id = eslp.employee_id AND lp.date_from COLLATE utf8_general_ci = eslp.date_from COLLATE utf8_general_ci AND lp.date_to COLLATE utf8_general_ci = eslp.date_to COLLATE utf8_general_ci JOIN tmp_1 AS tmp ON tmp.period_id = lp.id AND tmp.created_at = eslr.created_at ORDER BY eslrd.id");

        // creating temporary table for sick_leave_attachment
        $this->addSql("CREATE TABLE tmp_3( id int auto_increment primary key, employee_id int null, path varchar(100) not null unique, name varchar(100) not null, request_id int null, constraint FK_sick_leave_attachment2_request foreign key (request_id) references tmp_1 (id) on update cascade on delete cascade, constraint FK_sick_leave_attachment2_employee foreign key (employee_id) references employee (id))collate = utf8_unicode_ci");
        $this->addSql("INSERT INTO tmp_3 (employee_id, path, name, request_id) SELECT sla.employee_id, sla.path, sla.name,tmp.id FROM sick_leave_attachment AS sla JOIN employee_sick_leave_request AS eslr on sla.request_id = eslr.id JOIN employee_sick_leave_period eslp on eslr.period_id = eslp.id JOIN leave_period AS lp ON lp.employee_id = eslp.employee_id AND lp.date_from COLLATE utf8_general_ci = eslp.date_from COLLATE utf8_general_ci AND lp.date_to COLLATE utf8_general_ci = eslp.date_to COLLATE utf8_general_ci JOIN tmp_1 AS tmp ON tmp.period_id = lp.id AND tmp.created_at = eslr.created_at");
        $this->addSql("create table tmp_4 (sick_leave_request_id int not null, sick_leave_attachment_id int not null, primary key (sick_leave_request_id, sick_leave_attachment_id), constraint FK_sick_leave_request_attachment2_request foreign key (sick_leave_request_id) references tmp_1 (id) on delete cascade, constraint FK_sick_leave_request_attachment2_attachment foreign key (sick_leave_attachment_id) references tmp_3 (id) on delete cascade) collate = utf8_unicode_ci");

        // Removing old sick_leave tables and replacing them with new ones
        $this->addSql("DROP TABLE employee_sick_leave_request_day");
        $this->addSql("RENAME TABLE tmp_2 TO employee_sick_leave_request_day");
        $this->addSql("DROP TABLE sick_leave_request_attachment");
        $this->addSql("RENAME TABLE tmp_4 TO sick_leave_request_attachment");
        $this->addSql("DROP TABLE sick_leave_attachment");
        $this->addSql("RENAME TABLE tmp_3 TO sick_leave_attachment");
        $this->addSql("DROP TABLE employee_sick_leave_request");
        $this->addSql("RENAME TABLE tmp_1 TO employee_sick_leave_request");
        $this->addSql("DROP TABLE employee_sick_leave_period");
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("DROP TABLE leave_period");
    }
}
