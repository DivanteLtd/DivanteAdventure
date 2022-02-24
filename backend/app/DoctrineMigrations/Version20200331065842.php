<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200331065842 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql(<<<SQL
            CREATE TABLE config_entry (
                id INT AUTO_INCREMENT NOT NULL,
                responsible_id INT DEFAULT NULL,
                config_key VARCHAR(256) NOT NULL,
                config_value VARCHAR(512) NOT NULL,
                created_at DATETIME NOT NULL,
                replaced_at DATETIME DEFAULT NULL,
                INDEX IDX_960CCF30602AD315 (responsible_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE config_entry
                ADD CONSTRAINT FK_960CCF30602AD315 FOREIGN KEY (responsible_id)
                    REFERENCES employee (id) ON DELETE SET NULL
        SQL);
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.')
        ;
        $this->addSql('DROP TABLE config_entry');
    }
}
