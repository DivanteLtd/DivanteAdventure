<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200701113308 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A12576E0FD');

        $this->addSql(<<<SQL
            SET @var=IF(
                (SELECT true FROM information_schema.TABLE_CONSTRAINTS WHERE
                    CONSTRAINT_SCHEMA = DATABASE() AND
                    TABLE_NAME = 'employee' AND
                    CONSTRAINT_NAME = 'FK_employee_contract' AND
                    CONSTRAINT_TYPE = 'FOREIGN KEY') = true,
                'ALTER TABLE employee DROP FOREIGN KEY FK_employee_contract',
                'SELECT 1'
            )
        SQL);
        $this->addSql('PREPARE stmt FROM @var');
        $this->addSql('EXECUTE stmt');
        $this->addSql('DEALLOCATE PREPARE stmt');

        $this->addSql('DROP INDEX IDX_5D9F75A12576E0FD ON employee');
        $this->addSql('ALTER TABLE employee DROP mood');
        $this->addSql('ALTER TABLE agreement ADD contract_ids LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql(<<<SQL
            UPDATE agreement a
            JOIN (
                SELECT agreement_id,
                       GROUP_CONCAT(contract_id SEPARATOR ',') AS contract_ids
                FROM agreement_contract
                GROUP BY agreement_id
            ) c ON a.id = c.agreement_id
            SET a.contract_ids = c.contract_ids
        SQL);
        $this->addSql('DROP TABLE agreement_contract');
        $this->addSql('ALTER TABLE contract DROP freedays, DROP special, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('DROP TABLE contract');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE contract (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL COLLATE utf8_general_ci, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE employee ADD mood INT NOT NULL DEFAULT 0');
        $this->addSql('CREATE TABLE agreement_contract (agreement_id INT NOT NULL, contract_id INT NOT NULL, INDEX IDX_389C569C24890B2B (agreement_id), INDEX IDX_389C569C2576E0FD (contract_id), PRIMARY KEY(agreement_id, contract_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE agreement_contract ADD CONSTRAINT FK_389C569C24890B2B FOREIGN KEY (agreement_id) REFERENCES agreement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE agreement_contract ADD CONSTRAINT FK_389C569C2576E0FD FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE agreement DROP contract_ids');
        $this->addSql('ALTER TABLE contract ADD freedays TINYINT(1) DEFAULT \'0\', ADD special TINYINT(1) DEFAULT \'0\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A12576E0FD FOREIGN KEY (contract_id) REFERENCES contract (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_5D9F75A12576E0FD ON employee (contract_id)');
    }
}
