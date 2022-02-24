<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190404082921 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("DROP TABLE `news`");
        $this->addSql("CREATE TABLE `news` (`id` int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT, `author_id` INT NOT NULL, `title` varchar (255), `description` TEXT, `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8");
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD39950F675F31B FOREIGN KEY (author_id) REFERENCES employee (id)');
        $this->addSql('CREATE INDEX IDX_1DD39950F675F31B ON news (author_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql("DROP TABLE `news`");
        $this->addSql("CREATE TABLE `news` (`id` int(11) NOT NULL AUTO_INCREMENT, `title` varchar(255) COLLATE utf8_polish_ci NOT NULL, `description` text COLLATE utf8_polish_ci, `url` varchar(255) COLLATE utf8_polish_ci DEFAULT NULL, `important` tinyint(4) DEFAULT '0', `event_date` datetime DEFAULT NULL, `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci");
    }
}
