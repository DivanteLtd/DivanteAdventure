<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200203125149 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(<<<SQL
            CREATE TABLE faq_asked_question (
                id INT AUTO_INCREMENT NOT NULL,
                questioner_id INT NOT NULL,
                faq_category_id INT NOT NULL,
                question MEDIUMBLOB NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX IDX_196D1211CC0DE6E1 (questioner_id),
                INDEX IDX_196D1211F689B0DB (faq_category_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE faq_asked_question
                ADD CONSTRAINT FK_196D1211CC0DE6E1 FOREIGN KEY (questioner_id)
                    REFERENCES employee (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE faq_asked_question
                ADD CONSTRAINT FK_196D1211F689B0DB FOREIGN KEY (faq_category_id)
                    REFERENCES faq_category (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE faq_question
                CHANGE question_pl question_pl MEDIUMBLOB NOT NULL,
                CHANGE question_en question_en MEDIUMBLOB NOT NULL,
                CHANGE answer_pl answer_pl MEDIUMBLOB NOT NULL,
                CHANGE answer_en answer_en MEDIUMBLOB NOT NULL
        SQL);
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('DROP TABLE faq_asked_question');
        $this->addSql(<<<SQL
            ALTER TABLE faq_question
                CHANGE question_pl question_pl MEDIUMBLOB DEFAULT NULL,
                CHANGE question_en question_en MEDIUMBLOB DEFAULT NULL,
                CHANGE answer_pl answer_pl MEDIUMBLOB DEFAULT NULL,
                CHANGE answer_en answer_en MEDIUMBLOB DEFAULT NULL
        SQL);
    }
}
