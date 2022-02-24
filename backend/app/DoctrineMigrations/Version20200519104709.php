<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200519104709 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(<<<SQL
            CREATE TABLE planned_feedback (
                id INT AUTO_INCREMENT NOT NULL,
                employee_id INT NOT NULL,
                leader_id INT NOT NULL,
                date DATE NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX IDX_F3DD980A8C03F15C (employee_id),
                INDEX IDX_F3DD980A73154ED4 (leader_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE planned_feedback
                ADD CONSTRAINT FK_F3DD980A8C03F15C FOREIGN KEY (employee_id)
                    REFERENCES employee (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE planned_feedback
                ADD CONSTRAINT FK_F3DD980A73154ED4 FOREIGN KEY (leader_id)
                    REFERENCES employee (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('DROP TABLE planned_feedback');
    }
}
