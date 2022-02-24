<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180314230957 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employee ADD pesel VARCHAR(11) DEFAULT NULL, ADD nip VARCHAR(50) DEFAULT NULL, CHANGE remote remote INT DEFAULT NULL, CHANGE date_of_birth date_of_birth VARCHAR(255) DEFAULT NULL, CHANGE hired_at hired_at VARCHAR(255) DEFAULT NULL, CHANGE hired_to hired_to VARCHAR(255) DEFAULT NULL, CHANGE mood mood INT NOT NULL, CHANGE manager manager INT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL, CHANGE private_phone private_phone VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE agreement_attachment DROP FOREIGN KEY FK_786F779824890B2B');
        $this->addSql('ALTER TABLE agreement_attachment DROP FOREIGN KEY FK_786F7798464E68B');
        $this->addSql('ALTER TABLE agreement_attachment ADD CONSTRAINT FK_786F779824890B2B FOREIGN KEY (agreement_id) REFERENCES agreement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE agreement_attachment ADD CONSTRAINT FK_786F7798464E68B FOREIGN KEY (attachment_id) REFERENCES attachment (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employee DROP pesel, DROP nip, CHANGE private_phone private_phone VARCHAR(50) NOT NULL COLLATE utf8_general_ci, CHANGE remote remote TINYINT(1) DEFAULT NULL, CHANGE date_of_birth date_of_birth DATE DEFAULT NULL, CHANGE hired_at hired_at DATE DEFAULT NULL, CHANGE hired_to hired_to DATE DEFAULT NULL, CHANGE mood mood TINYINT(1) DEFAULT \'1\' NOT NULL, CHANGE manager manager TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE agreement_attachment DROP FOREIGN KEY FK_786F779824890B2B');
        $this->addSql('ALTER TABLE agreement_attachment DROP FOREIGN KEY FK_786F7798464E68B');
        $this->addSql('ALTER TABLE agreement_attachment ADD CONSTRAINT FK_786F779824890B2B FOREIGN KEY (agreement_id) REFERENCES agreement (id)');
        $this->addSql('ALTER TABLE agreement_attachment ADD CONSTRAINT FK_786F7798464E68B FOREIGN KEY (attachment_id) REFERENCES attachment (id)');
    }
}
