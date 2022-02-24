<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191031082536 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql('ALTER TABLE employee CHANGE hired_at hired_at DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE employee CHANGE hired_to hired_to DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE employee CHANGE date_of_birth date_of_birth DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE employee_end_cooperation CHANGE dismiss_date dismiss_date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE leave_period CHANGE date_from date_from DATE NOT NULL');
        $this->addSql('ALTER TABLE leave_period CHANGE date_to date_to DATE NOT NULL');
        $this->addSql('ALTER TABLE potential_employee CHANGE designated_hire_date designated_hire_date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE tribe_rotation_history CHANGE year year SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE tribe_rotation_history CHANGE month month SMALLINT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql('ALTER TABLE employee CHANGE hired_at hired_at VARCHAR(10) DEFAULT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE employee CHANGE hired_to hired_to VARCHAR(10) DEFAULT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE employee CHANGE date_of_birth date_of_birth VARCHAR(10) DEFAULT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE employee_end_cooperation CHANGE dismiss_date dismiss_date VARCHAR(10) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE leave_period CHANGE date_from date_from VARCHAR(10) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE leave_period CHANGE date_to date_to VARCHAR(10) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE potential_employee CHANGE designated_hire_date designated_hire_date VARCHAR(10) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE tribe_rotation_history CHANGE year year VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE tribe_rotation_history CHANGE month month INT NOT NULL');
    }
}
