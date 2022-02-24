<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190327064105 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE agreement_attachment_download_token (id INT AUTO_INCREMENT NOT NULL, attachment_id INT DEFAULT NULL, created_at DATETIME NOT NULL, token VARCHAR(64) NOT NULL, INDEX IDX_ED3F329F464E68B (attachment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE agreement_attachment_download_token ADD CONSTRAINT FK_ED3F329F464E68B FOREIGN KEY (attachment_id) REFERENCES attachment (id)');
        $this->addSql('ALTER TABLE agreement DROP type');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE agreement_attachment_download_token');
        $this->addSql('ALTER TABLE agreement ADD type VARCHAR(255) NOT NULL COLLATE utf8_general_ci');
    }
}
