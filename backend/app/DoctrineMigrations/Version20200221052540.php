<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200221052540 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql(<<<SQL
            ALTER TABLE hardware_agreement
                ADD assignment_id INT DEFAULT NULL,
                DROP name,
                DROP last_name,
                DROP contract,
                DROP category,
                DROP manufacturer,
                DROP model,
                DROP serial_number
        SQL);
        $this->addSql(<<<SQL
        ALTER TABLE hardware_agreement
            ADD CONSTRAINT FK_C6002FA6D19302F8 FOREIGN KEY (assignment_id)
                REFERENCES employee_hardware (id) ON DELETE CASCADE
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C6002FA6D19302F8 ON hardware_agreement (assignment_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql('ALTER TABLE hardware_agreement DROP FOREIGN KEY FK_C6002FA6D19302F8');
        $this->addSql('DROP INDEX UNIQ_C6002FA6D19302F8 ON hardware_agreement');
        $this->addSql(<<<SQL
            ALTER TABLE hardware_agreement
                ADD name VARCHAR(150) NOT NULL COLLATE utf8_unicode_ci,
                ADD last_name VARCHAR(150) NOT NULL COLLATE utf8_unicode_ci,
                ADD contract VARCHAR(10) NOT NULL COLLATE utf8_unicode_ci,
                ADD category VARCHAR(100) DEFAULT NULL COLLATE utf8_unicode_ci,
                ADD manufacturer VARCHAR(100) DEFAULT NULL COLLATE utf8_unicode_ci,
                ADD model VARCHAR(100) DEFAULT NULL COLLATE utf8_unicode_ci,
                ADD serial_number VARCHAR(100) DEFAULT NULL COLLATE utf8_unicode_ci,
                DROP assignment_id
        SQL);
    }
}
