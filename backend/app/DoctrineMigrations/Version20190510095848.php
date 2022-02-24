<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190510095848 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE level (id INT AUTO_INCREMENT NOT NULL, strategy_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, name VARCHAR(50) NOT NULL, INDEX IDX_9AEACC13D5CAD932 (strategy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE position (id INT AUTO_INCREMENT NOT NULL, strategy_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, name VARCHAR(50) NOT NULL, INDEX IDX_462CE4F5D5CAD932 (strategy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE position_tribe (position_id INT NOT NULL, tribe_id INT NOT NULL, INDEX IDX_AA935185DD842E46 (position_id), INDEX IDX_AA9351856F3EE0AD (tribe_id), PRIMARY KEY(position_id, tribe_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE leveling_strategy (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE level ADD CONSTRAINT FK_9AEACC13D5CAD932 FOREIGN KEY (strategy_id) REFERENCES leveling_strategy (id)');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT FK_462CE4F5D5CAD932 FOREIGN KEY (strategy_id) REFERENCES leveling_strategy (id)');
        $this->addSql('ALTER TABLE position_tribe ADD CONSTRAINT FK_AA935185DD842E46 FOREIGN KEY (position_id) REFERENCES position (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE position_tribe ADD CONSTRAINT FK_AA9351856F3EE0AD FOREIGN KEY (tribe_id) REFERENCES tribe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee ADD position_id INT DEFAULT NULL, ADD level_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1DD842E46 FOREIGN KEY (position_id) REFERENCES position (id)');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A15FB14BA7 FOREIGN KEY (level_id) REFERENCES level (id)');
        $this->addSql('CREATE INDEX IDX_5D9F75A1DD842E46 ON employee (position_id)');
        $this->addSql('CREATE INDEX IDX_5D9F75A15FB14BA7 ON employee (level_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A15FB14BA7');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1DD842E46');
        $this->addSql('ALTER TABLE position_tribe DROP FOREIGN KEY FK_AA935185DD842E46');
        $this->addSql('ALTER TABLE level DROP FOREIGN KEY FK_9AEACC13D5CAD932');
        $this->addSql('ALTER TABLE position DROP FOREIGN KEY FK_462CE4F5D5CAD932');
        $this->addSql('DROP TABLE level');
        $this->addSql('DROP TABLE position');
        $this->addSql('DROP TABLE position_tribe');
        $this->addSql('DROP TABLE leveling_strategy');
        $this->addSql('DROP INDEX IDX_5D9F75A1DD842E46 ON employee');
        $this->addSql('DROP INDEX IDX_5D9F75A15FB14BA7 ON employee');
        $this->addSql('ALTER TABLE employee DROP position_id, DROP level_id');
    }
}
