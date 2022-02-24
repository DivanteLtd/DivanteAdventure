<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190510080154 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1AE80F5DF');
        $this->addSql('ALTER TABLE position DROP FOREIGN KEY FK_position_department');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1DD842E46');
        $this->addSql('ALTER TABLE position DROP FOREIGN KEY FK_position_position');
        $this->addSql('ALTER TABLE position_skill DROP FOREIGN KEY FK_position_skill_position');
        $this->addSql('ALTER TABLE position_skill_area DROP FOREIGN KEY FK_position_skill_area_position');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE position');
        $this->addSql('DROP TABLE position_skill');
        $this->addSql('DROP TABLE position_skill_area');
        $this->addSql('DROP INDEX IDX_5D9F75A1DD842E46 ON employee');
        $this->addSql('DROP INDEX IDX_5D9F75A1AE80F5DF ON employee');
        $this->addSql('ALTER TABLE employee DROP position_id, DROP department_id');
        $this->addSql('ALTER TABLE employee RENAME INDEX fk_employee_contract TO IDX_5D9F75A12576E0FD');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A12576E0FD FOREIGN KEY (contract_id) REFERENCES contract (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE department (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(80) NOT NULL COLLATE utf8_general_ci, description TEXT DEFAULT NULL COLLATE utf8_general_ci, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, is_hidden_from_scheduler TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE position (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, department_id INT NOT NULL, name VARCHAR(50) NOT NULL COLLATE utf8_general_ci, background VARCHAR(50) DEFAULT NULL COLLATE utf8_general_ci, priority SMALLINT DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX FK_position_department (department_id), INDEX FK_position_position (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE position_skill (id INT AUTO_INCREMENT NOT NULL, position_id INT NOT NULL, skill_id INT NOT NULL, value_averaged DOUBLE PRECISION DEFAULT \'0\', created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX position_id_skill_id (position_id, skill_id), INDEX FK_position_skill_skill (skill_id), INDEX IDX_D2FD00AADD842E46 (position_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE position_skill_area (id INT AUTO_INCREMENT NOT NULL, position_id INT NOT NULL, skill_area_id INT NOT NULL, value_averaged DOUBLE PRECISION DEFAULT \'0\', created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX position_id_skill_area_id (position_id, skill_area_id), INDEX FK_position_skill_area_skill_area (skill_area_id), INDEX IDX_BE348D5ADD842E46 (position_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT FK_position_department FOREIGN KEY (department_id) REFERENCES department (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT FK_position_position FOREIGN KEY (parent_id) REFERENCES position (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE position_skill ADD CONSTRAINT FK_position_skill_position FOREIGN KEY (position_id) REFERENCES position (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE position_skill ADD CONSTRAINT FK_position_skill_skill FOREIGN KEY (skill_id) REFERENCES skill (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE position_skill_area ADD CONSTRAINT FK_position_skill_area_position FOREIGN KEY (position_id) REFERENCES position (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE position_skill_area ADD CONSTRAINT FK_position_skill_area_skill_area FOREIGN KEY (skill_area_id) REFERENCES skill_area (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A12576E0FD');
        $this->addSql('ALTER TABLE employee ADD position_id INT DEFAULT NULL, ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1DD842E46 FOREIGN KEY (position_id) REFERENCES position (id)');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_employee_contract FOREIGN KEY (contract_id) REFERENCES contract (id) ON UPDATE CASCADE ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_5D9F75A1DD842E46 ON employee (position_id)');
        $this->addSql('CREATE INDEX IDX_5D9F75A1AE80F5DF ON employee (department_id)');
        $this->addSql('ALTER TABLE employee RENAME INDEX idx_5d9f75a12576e0fd TO FK_employee_contract');
    }
}
