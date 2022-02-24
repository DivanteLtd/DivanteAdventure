<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180830073050 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE employee_daysoff_request ADD resignation INT NOT NULL, CHANGE employee_id employee_id INT DEFAULT NULL, CHANGE period_id period_id INT DEFAULT NULL, CHANGE status status INT NOT NULL, CHANGE accepted_at accepted_at DATETIME NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE employee_sick_leave_request ADD resignation INT NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE employee_daysoff_request CHANGE COLUMN accepted_at accepted_at DATETIME NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE employee_daysoff_request DROP resignation, CHANGE period_id period_id INT NOT NULL, CHANGE employee_id employee_id INT NOT NULL, CHANGE status status TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE accepted_at accepted_at DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE employee_sick_leave_request DROP resignation, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE employee_daysoff_request CHANGE COLUMN accepted_at accepted_at DATETIME NOT NULL');
    }
}
