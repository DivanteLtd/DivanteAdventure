<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200210071108 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE potential_employee CHANGE suggested_email email VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE employee_hardware ADD potential_employee_id INT DEFAULT NULL, CHANGE employee_id employee_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employee_hardware ADD CONSTRAINT FK_6EA6B82789EFB0AC FOREIGN KEY (potential_employee_id) REFERENCES potential_employee (id)');
        $this->addSql('CREATE INDEX IDX_6EA6B82789EFB0AC ON employee_hardware (potential_employee_id)');
        $this->addSql('CREATE TABLE hardware_agreement (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, last_name VARCHAR(150) NOT NULL, contract VARCHAR(10) NOT NULL, category VARCHAR(100) DEFAULT NULL, manufacturer VARCHAR(100) DEFAULT NULL, model VARCHAR(100) DEFAULT NULL, serialNumber VARCHAR(100) DEFAULT NULL, signed_by_adm DATETIME DEFAULT NULL, signed_by_hosting DATETIME DEFAULT NULL, signed_by_user DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE potential_employee CHANGE email suggested_email VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE employee_hardware DROP potential_employee_id, CHANGE employee_id employee_id INT NOT NULL');
        $this->addSql('ALTER TABLE employee_hardware DROP FOREIGN KEY FK_6EA6B82789EFB0AC');
        $this->addSql('DROP INDEX IDX_6EA6B82789EFB0AC ON employee_hardware');
        $this->addSql('DROP TABLE hardware_agreement');
    }
}
