<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190703085813 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tribe_rotation_history (id INT AUTO_INCREMENT NOT NULL, employee_id INT NOT NULL, old_tribe_id INT DEFAULT NULL, new_tribe_id INT DEFAULT NULL, action VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_741D33F18C03F15C (employee_id), INDEX IDX_741D33F14262480 (old_tribe_id), INDEX IDX_741D33F128576C81 (new_tribe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tribe_rotation_history ADD CONSTRAINT FK_741D33F18C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE tribe_rotation_history ADD CONSTRAINT FK_741D33F14262480 FOREIGN KEY (old_tribe_id) REFERENCES tribe (id)');
        $this->addSql('ALTER TABLE tribe_rotation_history ADD CONSTRAINT FK_741D33F128576C81 FOREIGN KEY (new_tribe_id) REFERENCES tribe (id)');
     }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE tribe_rotation_history');
    }
}
