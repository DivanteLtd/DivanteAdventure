<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201027100449 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employee ADD tech_tribe_leader TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE tribe ADD tech_leader INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tribe ADD CONSTRAINT FK_2653B558E5EE8BEC FOREIGN KEY (tech_leader) REFERENCES employee (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_2653B558E5EE8BEC ON tribe (tech_leader)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employee DROP tech_tribe_leader');
        $this->addSql('ALTER TABLE tribe DROP FOREIGN KEY FK_2653B558E5EE8BEC');
        $this->addSql('DROP INDEX IDX_2653B558E5EE8BEC ON tribe');
        $this->addSql('ALTER TABLE tribe DROP tech_leader');
    }
}
