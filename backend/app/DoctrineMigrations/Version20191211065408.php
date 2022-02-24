<?php /** @noinspection SqlResolve */

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191211065408 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('
            CREATE TABLE checklist (
                id INT AUTO_INCREMENT NOT NULL,
                owner_id INT DEFAULT NULL,
                subject_id INT NOT NULL,
                type SMALLINT NOT NULL,
                name_pl VARCHAR(255) NOT NULL,
                name_en VARCHAR(255) NOT NULL,
                started_at DATETIME NOT NULL,
                finished_at DATETIME DEFAULT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX IDX_5C696D2F7E3C61F9 (owner_id),
                INDEX IDX_5C696D2F23EDC87 (subject_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE checklist_template (
                id INT AUTO_INCREMENT NOT NULL,
                type SMALLINT NOT NULL,
                name_pl VARCHAR(255) NOT NULL,
                name_en VARCHAR(255) NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE checklist_template_question (
                id INT AUTO_INCREMENT NOT NULL,
                responsible_id INT DEFAULT NULL,
                checklist_id INT NOT NULL,
                name_pl VARCHAR(255) NOT NULL,
                name_en VARCHAR(255) NOT NULL,
                description_pl VARCHAR(255) NOT NULL,
                description_en VARCHAR(255) NOT NULL,
                possible_statuses JSON NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX IDX_C030E916602AD315 (responsible_id),
                INDEX IDX_C030E916B16D08A7 (checklist_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            CREATE TABLE checklist_question (
                id INT AUTO_INCREMENT NOT NULL,
                responsible_id INT DEFAULT NULL,
                checklist_id INT NOT NULL,
                name_pl VARCHAR(255) NOT NULL,
                name_en VARCHAR(255) NOT NULL,
                description_pl VARCHAR(255) NOT NULL,
                description_en VARCHAR(255) NOT NULL,
                possible_statuses JSON NOT NULL,
                current_status INT NOT NULL,
                checked_at DATETIME DEFAULT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX IDX_EBF3358C602AD315 (responsible_id),
                INDEX IDX_EBF3358CB16D08A7 (checklist_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB
        ');
        $this->addSql('
            ALTER TABLE checklist
                ADD CONSTRAINT FK_5C696D2F7E3C61F9
                    FOREIGN KEY (owner_id)
                    REFERENCES employee (id)
        ');
        $this->addSql('
            ALTER TABLE checklist
                ADD CONSTRAINT FK_5C696D2F23EDC87
                    FOREIGN KEY (subject_id)
                    REFERENCES employee (id)
        ');
        $this->addSql('
            ALTER TABLE checklist_template_question
                ADD CONSTRAINT FK_C030E916602AD315
                    FOREIGN KEY (responsible_id)
                    REFERENCES employee (id)
        ');
        $this->addSql('
            ALTER TABLE checklist_template_question
                ADD CONSTRAINT FK_C030E916B16D08A7
                    FOREIGN KEY (checklist_id)
                    REFERENCES checklist_template (id)
        ');
        $this->addSql('
            ALTER TABLE checklist_question
                ADD CONSTRAINT FK_EBF3358C602AD315
                    FOREIGN KEY (responsible_id)
                    REFERENCES employee (id)
        ');
        $this->addSql('
            ALTER TABLE checklist_question
                ADD CONSTRAINT FK_EBF3358CB16D08A7
                    FOREIGN KEY (checklist_id)
                    REFERENCES checklist (id)
        ');
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE checklist_question DROP FOREIGN KEY FK_EBF3358CB16D08A7');
        $this->addSql('ALTER TABLE checklist_template_question DROP FOREIGN KEY FK_C030E916B16D08A7');
        $this->addSql('DROP TABLE checklist');
        $this->addSql('DROP TABLE checklist_template_question');
        $this->addSql('DROP TABLE checklist_template');
        $this->addSql('DROP TABLE checklist_question');
        $this->addSql('DROP TABLE slack_message_queue');
    }
}
