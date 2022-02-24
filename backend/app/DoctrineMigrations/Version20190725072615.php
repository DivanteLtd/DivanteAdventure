<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190725072615 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE data_processing_history DROP FOREIGN KEY FK_665122EF166D1F9C');
        $this->addSql('ALTER TABLE data_processing_history ADD CONSTRAINT FK_665122EF166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE data_processing_history DROP FOREIGN KEY FK_665122EF166D1F9C');
        $this->addSql('ALTER TABLE data_processing_history ADD CONSTRAINT FK_665122EF166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');

    }
}
