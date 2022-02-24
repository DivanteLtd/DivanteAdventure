<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190913104600 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE IF EXISTS tribe_rotation_history');
        $this->addSql('CREATE TABLE tribe_rotation_history (id INT AUTO_INCREMENT NOT NULL, tribe_name VARCHAR(50) NOT NULL, number_of_enter INT DEFAULT 0 NOT NULL, number_of_leave INT DEFAULT 0 NOT NULL, number_of_work INT DEFAULT 0 NOT NULL, number_of_male INT DEFAULT 0 NOT NULL, number_of_female INT DEFAULT 0 NOT NULL, year VARCHAR(255) NOT NULL, month INT NOT NULL, employees VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');

    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE tribe_rotation_history');
     }
}
