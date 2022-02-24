<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200226092051 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE hardware_agreement DROP INDEX UNIQ_C6002FA615E7B47B, ADD INDEX IDX_C6002FA615E7B47B (helpdesk_signer_id)');
        $this->addSql('ALTER TABLE hardware_agreement DROP INDEX UNIQ_C6002FA6579BC01D, ADD INDEX IDX_C6002FA6579BC01D (adm_signer_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE hardware_agreement DROP INDEX IDX_C6002FA615E7B47B, ADD UNIQUE INDEX UNIQ_C6002FA615E7B47B (helpdesk_signer_id)');
        $this->addSql('ALTER TABLE hardware_agreement DROP INDEX IDX_C6002FA6579BC01D, ADD UNIQUE INDEX UNIQ_C6002FA6579BC01D (adm_signer_id)');
    }
}
