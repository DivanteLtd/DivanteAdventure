<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200326104017 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tribe_responsible (tribe_id INT NOT NULL, employee_id INT NOT NULL, INDEX IDX_7E38AF476F3EE0AD (tribe_id), INDEX IDX_7E38AF478C03F15C (employee_id), PRIMARY KEY(tribe_id, employee_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tribe_responsible ADD CONSTRAINT FK_7E38AF476F3EE0AD FOREIGN KEY (tribe_id) REFERENCES tribe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tribe_responsible ADD CONSTRAINT FK_7E38AF478C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE tribe_responsible');
    }
}
