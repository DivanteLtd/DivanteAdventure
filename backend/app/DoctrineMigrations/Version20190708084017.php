<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190708084017 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE potential_employee (id INT AUTO_INCREMENT NOT NULL, designated_tribe_id INT DEFAULT NULL, designated_position_id INT DEFAULT NULL, joined_employee_id INT DEFAULT NULL, name VARCHAR(150) NOT NULL, lastname VARCHAR(150) NOT NULL, suggested_email VARCHAR(150) NOT NULL, designated_hire_date VARCHAR(12) DEFAULT NULL, status INT NOT NULL, rejection_cause VARCHAR(500) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_26DD2863BCE1E7BB (designated_tribe_id), INDEX IDX_26DD28639C02A8D5 (designated_position_id), UNIQUE INDEX UNIQ_26DD286331AACC7 (joined_employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE potential_employee ADD CONSTRAINT FK_26DD2863BCE1E7BB FOREIGN KEY (designated_tribe_id) REFERENCES tribe (id)');
        $this->addSql('ALTER TABLE potential_employee ADD CONSTRAINT FK_26DD28639C02A8D5 FOREIGN KEY (designated_position_id) REFERENCES position (id)');
        $this->addSql('ALTER TABLE potential_employee ADD CONSTRAINT FK_26DD286331AACC7 FOREIGN KEY (joined_employee_id) REFERENCES employee (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE potential_employee');
    }
}
