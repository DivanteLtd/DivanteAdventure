<?php declare(strict_types=1);

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200302105653 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql('ALTER TABLE employee_end_cooperation DROP FOREIGN KEY FK_2782FE308C03F15C');
        $this->addSql(<<<SQL
            ALTER TABLE employee_end_cooperation
                ADD CONSTRAINT FK_2782FE308C03F15C FOREIGN KEY (employee_id)
                    REFERENCES employee (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql('ALTER TABLE employee_end_cooperation DROP FOREIGN KEY FK_2782FE308C03F15C');
        $this->addSql(<<<SQL
            ALTER TABLE employee_end_cooperation
                ADD CONSTRAINT FK_2782FE308C03F15C FOREIGN KEY (employee_id)
                    REFERENCES employee (id)
        SQL);
    }
}
