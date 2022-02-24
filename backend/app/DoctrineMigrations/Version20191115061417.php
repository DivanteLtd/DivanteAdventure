<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191115061417 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql('
            CREATE TABLE hardware_assignment (
                id INT AUTO_INCREMENT NOT NULL,
                hardware_id INT NOT NULL,
                employee_id INT NOT NULL,
                start_date DATE NOT NULL,
                end_date DATE DEFAULT NULL,
                notes VARCHAR(1023) DEFAULT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX IDX_339BC3C3C9CC762B (hardware_id),
                INDEX IDX_339BC3C38C03F15C (employee_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE hardware (
                id INT AUTO_INCREMENT NOT NULL,
                created_by_id INT NOT NULL,
                category VARCHAR(255) NOT NULL,
                brand VARCHAR(255) DEFAULT NULL,
                model VARCHAR(255) DEFAULT NULL,
                serial_number VARCHAR(255) NOT NULL,
                notes VARCHAR(1023) DEFAULT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                deleted_at DATETIME DEFAULT NULL,
                INDEX IDX_FE99E9E0B03A8386 (created_by_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            ALTER TABLE hardware_assignment
                ADD CONSTRAINT FK_339BC3C3C9CC762B FOREIGN KEY (hardware_id) REFERENCES hardware (id),
                ADD CONSTRAINT FK_339BC3C38C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)
        ');
        $this->addSql('
            ALTER TABLE hardware
                ADD CONSTRAINT FK_FE99E9E0B03A8386 FOREIGN KEY (created_by_id) REFERENCES employee (id)
        ');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE hardware_assignment DROP FOREIGN KEY FK_339BC3C3C9CC762B');
        $this->addSql('DROP TABLE hardware_assignment');
        $this->addSql('DROP TABLE hardware');
    }
}
