<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200819093134 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE leaders (employee_id INT NOT NULL, leader_id INT NOT NULL, INDEX IDX_34C6B968C03F15C (employee_id), INDEX IDX_34C6B9673154ED4 (leader_id), PRIMARY KEY(employee_id, leader_id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE leaders ADD CONSTRAINT FK_34C6B968C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE leaders ADD CONSTRAINT FK_34C6B9673154ED4 FOREIGN KEY (leader_id) REFERENCES employee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A173154ED4');
        $this->addSql('DROP INDEX IDX_5D9F75A173154ED4 ON employee');
        $this->addSql('ALTER TABLE employee DROP leader_id');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE leaders');
        $this->addSql('ALTER TABLE employee ADD leader_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A173154ED4 FOREIGN KEY (leader_id) REFERENCES employee (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_5D9F75A173154ED4 ON employee (leader_id)');
    }
}
