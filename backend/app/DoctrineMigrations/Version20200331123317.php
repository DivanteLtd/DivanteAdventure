<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200331123317 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE hardware_agreement DROP FOREIGN KEY FK_C6002FA6579BC01D');
        $this->addSql('DROP INDEX IDX_C6002FA6579BC01D ON hardware_agreement');
        $this->addSql('ALTER TABLE hardware_agreement DROP adm_signer_id, DROP signed_by_adm');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE hardware_agreement ADD adm_signer_id INT DEFAULT NULL, ADD signed_by_adm DATETIME DEFAULT NULL, CHANGE pesel pesel BLOB DEFAULT NULL, CHANGE nip nip BLOB DEFAULT NULL, CHANGE company company BLOB DEFAULT NULL, CHANGE headquarters headquarters BLOB DEFAULT NULL');
        $this->addSql('ALTER TABLE hardware_agreement ADD CONSTRAINT FK_C6002FA6579BC01D FOREIGN KEY (adm_signer_id) REFERENCES employee (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_C6002FA6579BC01D ON hardware_agreement (adm_signer_id)');
    }
}
