<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190730115310 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE evidence_invoice_attachment (id INT AUTO_INCREMENT NOT NULL, evidence_id INT DEFAULT NULL, owner_employee_id INT DEFAULT NULL, path VARCHAR(100) NOT NULL, name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_1566BC78B528FC11 (evidence_id), INDEX IDX_1566BC788AE72EDA (owner_employee_id), UNIQUE INDEX path (path), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evidence_invoice_attachment ADD CONSTRAINT FK_1566BC78B528FC11 FOREIGN KEY (evidence_id) REFERENCES evidence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evidence_invoice_attachment ADD CONSTRAINT FK_1566BC788AE72EDA FOREIGN KEY (owner_employee_id) REFERENCES employee (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE evidence_invoice_attachment');
    }
}
