<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180322101148 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE data_processing_criterium (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(80) NOT NULL, UNIQUE INDEX UNIQ_1AA881CE5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_data_processing_criterium (project_id INT NOT NULL, data_processing_criterium_id INT NOT NULL, INDEX IDX_623B331B166D1F9C (project_id), INDEX IDX_623B331B7E5FCB0 (data_processing_criterium_id), PRIMARY KEY(project_id, data_processing_criterium_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_data_processing_criterium ADD CONSTRAINT FK_623B331B166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_data_processing_criterium ADD CONSTRAINT FK_623B331B7E5FCB0 FOREIGN KEY (data_processing_criterium_id) REFERENCES data_processing_criterium (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE project_data_processing_criterium DROP FOREIGN KEY FK_623B331B7E5FCB0');
        $this->addSql('DROP TABLE data_processing_criterium');
        $this->addSql('DROP TABLE project_data_processing_criterium');
    }
}
