<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180312095646 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE agreement_contract (agreement_id INT NOT NULL, contract_id INT NOT NULL, INDEX IDX_389C569C24890B2B (agreement_id), INDEX IDX_389C569C2576E0FD (contract_id), PRIMARY KEY(agreement_id, contract_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE agreement_attachment (agreement_id INT NOT NULL, attachment_id INT NOT NULL, INDEX IDX_786F779824890B2B (agreement_id), INDEX IDX_786F7798464E68B (attachment_id), PRIMARY KEY(agreement_id, attachment_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attachment (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(100) NOT NULL, name VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_795FD9BBB548B0F (path), UNIQUE INDEX UNIQ_795FD9BB5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE agreement_contract ADD CONSTRAINT FK_389C569C24890B2B FOREIGN KEY (agreement_id) REFERENCES agreement (id)');
        $this->addSql('ALTER TABLE agreement_contract ADD CONSTRAINT FK_389C569C2576E0FD FOREIGN KEY (contract_id) REFERENCES contract (id)');
        $this->addSql('ALTER TABLE agreement_attachment ADD CONSTRAINT FK_786F779824890B2B FOREIGN KEY (agreement_id) REFERENCES agreement (id)');
        $this->addSql('ALTER TABLE agreement_attachment ADD CONSTRAINT FK_786F7798464E68B FOREIGN KEY (attachment_id) REFERENCES attachment (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE agreement_attachment DROP FOREIGN KEY FK_786F7798464E68B');
        $this->addSql('DROP TABLE agreement_contract');
        $this->addSql('DROP TABLE agreement_attachment');
        $this->addSql('DROP TABLE attachment');
    }
}
