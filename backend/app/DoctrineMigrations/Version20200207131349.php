<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200207131349 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE employee_hardware ADD asset_id INT NOT NULL');
        $this->addSql('ALTER TABLE employee_hardware ADD send_email TINYINT(1) DEFAULT \'0\' NOT NULL');

    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE employee_hardware DROP asset_id');
        $this->addSql('ALTER TABLE employee_hardware DROP send_email');
    }
}
