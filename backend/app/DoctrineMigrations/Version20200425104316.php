<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200425104316 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE checklist_owner (checklist_id INT NOT NULL, employee_id INT NOT NULL, INDEX IDX_F18861A2B16D08A7 (checklist_id), INDEX IDX_F18861A28C03F15C (employee_id), PRIMARY KEY(checklist_id, employee_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE checklist_owner ADD CONSTRAINT FK_F18861A2B16D08A7 FOREIGN KEY (checklist_id) REFERENCES checklist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE checklist_owner ADD CONSTRAINT FK_F18861A28C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE');

    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE checklist_owner');
    }
}
