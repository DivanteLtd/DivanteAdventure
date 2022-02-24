<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200320115719 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE employee ADD leader_id INT DEFAULT NULL');
        $this->addSql(
            'ALTER TABLE employee
                ADD CONSTRAINT FK_5D9F75A173154ED4 FOREIGN KEY (leader_id)
                REFERENCES employee (id) ON DELETE SET NULL'
        );
        $this->addSql('CREATE INDEX IDX_5D9F75A173154ED4 ON employee (leader_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A173154ED4');
        $this->addSql('DROP INDEX IDX_5D9F75A173154ED4 ON employee');
        $this->addSql('ALTER TABLE employee DROP leader_id');
    }
}
