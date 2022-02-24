<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200224080841 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql(<<<SQL
            ALTER TABLE hardware_agreement
                ADD helpdesk_signer_id INT DEFAULT NULL,
                ADD adm_signer_id INT DEFAULT NULL,
                ADD use_languages TINYTEXT NOT NULL COMMENT '(DC2Type:simple_array)'      
        SQL);
        $this->addSQL(<<<SQL
            ALTER TABLE hardware_agreement
                ADD CONSTRAINT FK_C6002FA615E7B47B FOREIGN KEY (helpdesk_signer_id)
                    REFERENCES employee (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE hardware_agreement
                ADD CONSTRAINT FK_C6002FA6579BC01D FOREIGN KEY (adm_signer_id)
                    REFERENCES employee (id) ON DELETE CASCADE
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C6002FA615E7B47B ON hardware_agreement (helpdesk_signer_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C6002FA6579BC01D ON hardware_agreement (adm_signer_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql('ALTER TABLE hardware_agreement DROP FOREIGN KEY FK_C6002FA615E7B47B');
        $this->addSql('ALTER TABLE hardware_agreement DROP FOREIGN KEY FK_C6002FA6579BC01D');
        $this->addSql('DROP INDEX UNIQ_C6002FA615E7B47B ON hardware_agreement');
        $this->addSql('DROP INDEX UNIQ_C6002FA6579BC01D ON hardware_agreement');
        $this->addSql(<<<SQL
            ALTER TABLE hardware_agreement
                DROP helpdesk_signer_id,
                DROP adm_signer_id,
                DROP use_languages
        SQL);
    }
}
