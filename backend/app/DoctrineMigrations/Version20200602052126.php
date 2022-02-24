<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200602052126 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql('ALTER TABLE feedback ADD progress_feedback MEDIUMBLOB DEFAULT NULL');
        $this->addSql('ALTER TABLE feedback ADD technical_feedback MEDIUMBLOB DEFAULT NULL');
        $this->addSql('ALTER TABLE feedback RENAME INDEX fk_d22944588c03f15c TO IDX_D22944588C03F15C');
        $this->addSql('ALTER TABLE feedback RENAME INDEX fk_d229445873154ed4 TO IDX_D229445873154ED4');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql('ALTER TABLE feedback DROP progress_feedback, DROP technical_feedback');
        $this->addSql('ALTER TABLE feedback RENAME INDEX idx_d229445873154ed4 TO FK_D229445873154ED4');
        $this->addSql('ALTER TABLE feedback RENAME INDEX idx_d22944588c03f15c TO FK_D22944588C03F15C');
    }
}
