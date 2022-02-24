<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200116072748 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE faq_category (id INT AUTO_INCREMENT NOT NULL, name_pl VARCHAR(50) NOT NULL, name_en VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE faq_category_responsible (faqcategory_id INT NOT NULL, employee_id INT NOT NULL, INDEX IDX_E8F53515B0EF5F16 (faqcategory_id), INDEX IDX_E8F535158C03F15C (employee_id), PRIMARY KEY(faqcategory_id, employee_id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE faq_question (id INT AUTO_INCREMENT NOT NULL, employee_id INT NOT NULL, faq_category_id INT NOT NULL, question_pl VARCHAR(50) NOT NULL, question_en VARCHAR(50) NOT NULL, answer_pl VARCHAR(50) NOT NULL, answer_en VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_4A55B0598C03F15C (employee_id), INDEX IDX_4A55B059F689B0DB (faq_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE faq_category_responsible ADD CONSTRAINT FK_E8F53515B0EF5F16 FOREIGN KEY (faqcategory_id) REFERENCES faq_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE faq_category_responsible ADD CONSTRAINT FK_E8F535158C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE faq_question ADD CONSTRAINT FK_4A55B0598C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE faq_question ADD CONSTRAINT FK_4A55B059F689B0DB FOREIGN KEY (faq_category_id) REFERENCES faq_category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE faq_category_responsible DROP FOREIGN KEY FK_E8F53515B0EF5F16');
        $this->addSql('ALTER TABLE faq_question DROP FOREIGN KEY FK_4A55B059F689B0DB');
        $this->addSql('DROP TABLE slack_message_queue');
        $this->addSql('DROP TABLE faq_category');
        $this->addSql('DROP TABLE faq_category_responsible');
        $this->addSql('DROP TABLE faq_question');
    }
}
