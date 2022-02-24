<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200121095750 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE faq_category_responsible RENAME INDEX idx_e8f53515b0ef5f16 TO IDX_771CD07BB0EF5F16');
        $this->addSql('ALTER TABLE faq_category_responsible RENAME INDEX idx_e8f535158c03f15c TO IDX_771CD07B8C03F15C');
        $this->addSql('ALTER TABLE faq_question CHANGE question_pl question_pl mediumblob, CHANGE question_en question_en mediumblob, CHANGE answer_pl answer_pl mediumblob, CHANGE answer_en answer_en mediumblob');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE faq_category_responsible RENAME INDEX idx_771cd07bb0ef5f16 TO IDX_E8F53515B0EF5F16');
        $this->addSql('ALTER TABLE faq_category_responsible RENAME INDEX idx_771cd07b8c03f15c TO IDX_E8F535158C03F15C');
        $this->addSql('ALTER TABLE faq_question CHANGE question_pl question_pl VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, CHANGE question_en question_en VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, CHANGE answer_pl answer_pl VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, CHANGE answer_en answer_en VARCHAR(50) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
    }
}
