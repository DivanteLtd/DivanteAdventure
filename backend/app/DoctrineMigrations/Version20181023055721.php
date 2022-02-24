<?php

namespace Adventure\Migrations;

use Divante\Bundle\AdventureBundle\Entity\Project;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181023055721 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE employee_project DROP hours');
        $this->addSql('ALTER TABLE project ADD sum_type INT NOT NULL DEFAULT '.Project::PROJECT_HOURS_SUM_MONTHLY);
    }
    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE employee_project ADD hours SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE project DROP sum_type');
    }
}
