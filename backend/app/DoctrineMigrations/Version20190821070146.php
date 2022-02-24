<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190821070146 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE toggl_project (id INT AUTO_INCREMENT NOT NULL, toggl_id INT NOT NULL, name VARCHAR(250) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_E1A5A311E6D5BC29 (toggl_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE toggl_project_mapping (toggl_project_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_A7995B835833B4DB (toggl_project_id), INDEX IDX_A7995B83166D1F9C (project_id), PRIMARY KEY(toggl_project_id, project_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE toggl_project_mapping ADD CONSTRAINT FK_A7995B835833B4DB FOREIGN KEY (toggl_project_id) REFERENCES toggl_project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE toggl_project_mapping ADD CONSTRAINT FK_A7995B83166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee ADD toggl_id INT DEFAULT NULL');
        $this->addSql('CREATE TABLE integration_queue (id INT AUTO_INCREMENT NOT NULL, employee_id INT NOT NULL, project_id INT NOT NULL, type VARCHAR(255) NOT NULL, status INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_F5AD28B28C03F15C (employee_id), INDEX IDX_F5AD28B2166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE integration_queue ADD CONSTRAINT FK_F5AD28B28C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE integration_queue ADD CONSTRAINT FK_F5AD28B2166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee_project ADD toggl_pairing_ids VARCHAR(150) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE toggl_project_mapping DROP FOREIGN KEY FK_A7995B835833B4DB');
        $this->addSql('DROP TABLE toggl_project');
        $this->addSql('DROP TABLE toggl_project_mapping');
        $this->addSql('ALTER TABLE employee DROP toggl_id');
        $this->addSql('DROP TABLE integration_queue');
    }
}
