<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200713090921 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql(<<<SQL
            ALTER TABLE tribe
                ADD sick_leave_project_id VARCHAR(50) DEFAULT NULL,
                ADD sick_leave_category_id VARCHAR(50) DEFAULT NULL,
                ADD free_day_project_id VARCHAR(50) DEFAULT NULL,
                ADD free_day_category_id VARCHAR(50) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql(<<<SQL
            ALTER TABLE tribe
                DROP sick_leave_project_id,
                DROP sick_leave_category_id,
                DROP free_day_project_id,
                DROP free_day_category_id
        SQL);
    }
}
