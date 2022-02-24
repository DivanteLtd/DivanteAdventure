<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200416055653 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE fos_user DROP FOREIGN KEY FK_957A64798C03F15C');
        $this->addSql('ALTER TABLE fos_user ADD CONSTRAINT FK_957A64798C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE fos_user DROP FOREIGN KEY FK_957A64798C03F15C');
        $this->addSql('ALTER TABLE fos_user ADD CONSTRAINT FK_957A64798C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
    }
}
