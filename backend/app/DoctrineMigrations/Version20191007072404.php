<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191007072404 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE gitlab_project (id INT AUTO_INCREMENT NOT NULL, gitlab_id INT NOT NULL, gitlab_type INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gitlab_project_mapping (gitlab_project_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_BD92D3715D07572E (gitlab_project_id), INDEX IDX_BD92D371166D1F9C (project_id), PRIMARY KEY(gitlab_project_id, project_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gitlab_project_mapping ADD CONSTRAINT FK_BD92D3715D07572E FOREIGN KEY (gitlab_project_id) REFERENCES gitlab_project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gitlab_project_mapping ADD CONSTRAINT FK_BD92D371166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee ADD gitlab_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE gitlab_project_mapping DROP FOREIGN KEY FK_BD92D3715D07572E');
        $this->addSql('DROP TABLE gitlab_project');
        $this->addSql('DROP TABLE gitlab_project_mapping');
        $this->addSql('ALTER TABLE employee DROP gitlab_id');
    }
}
