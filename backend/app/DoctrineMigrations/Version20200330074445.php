<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200330074445 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE employee_toggl_access DROP FOREIGN KEY FK_5FCB60635833B4DB');
        $this->addSql('DROP TABLE employee_toggl_access');
        $this->addSql('ALTER TABLE toggl_project_mapping DROP FOREIGN KEY FK_A7995B835833B4DB');
        $this->addSql('DROP TABLE toggl_project_mapping');
        $this->addSql('DROP TABLE toggl_project');
        $this->addSql('ALTER TABLE employee DROP toggl_api_key, DROP toggl_id');
        $this->addSql('ALTER TABLE leave_request_day DROP toggl_status, DROP toggl_id, DROP toggl_error');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(<<<SQL
            CREATE TABLE employee_toggl_access (
                id INT AUTO_INCREMENT NOT NULL,
                employee_id INT NOT NULL,
                toggl_project_id INT NOT NULL,
                pairing_id VARCHAR(150) NOT NULL COLLATE utf8_unicode_ci,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                INDEX IDX_5FCB60638C03F15C (employee_id),
                INDEX IDX_5FCB60635833B4DB (toggl_project_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<SQL
            CREATE TABLE toggl_project (
                id INT AUTO_INCREMENT NOT NULL,
                toggl_id INT NOT NULL,
                name VARCHAR(250) NOT NULL COLLATE utf8_unicode_ci,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                UNIQUE INDEX UNIQ_E1A5A311E6D5BC29 (toggl_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<SQL
            CREATE TABLE toggl_project_mapping (
                toggl_project_id INT NOT NULL,
                project_id INT NOT NULL,
                INDEX IDX_A7995B83166D1F9C (project_id),
                INDEX IDX_A7995B835833B4DB (toggl_project_id),
                PRIMARY KEY(toggl_project_id, project_id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE employee_toggl_access
                ADD CONSTRAINT FK_5FCB60635833B4DB FOREIGN KEY (toggl_project_id)
                    REFERENCES toggl_project (id)
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE employee_toggl_access
                ADD CONSTRAINT FK_5FCB60638C03F15C FOREIGN KEY (employee_id)
                    REFERENCES employee (id)
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE toggl_project_mapping
                ADD CONSTRAINT FK_A7995B83166D1F9C FOREIGN KEY (project_id)
                    REFERENCES project (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE toggl_project_mapping
                ADD CONSTRAINT FK_A7995B835833B4DB FOREIGN KEY (toggl_project_id)
                    REFERENCES toggl_project (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE employee
                ADD toggl_api_key VARCHAR(50) DEFAULT NULL COLLATE utf8_general_ci,
                ADD toggl_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<SQL
            ALTER TABLE leave_request_day ADD
                toggl_status SMALLINT DEFAULT 1 NOT NULL,
                ADD toggl_id VARCHAR(255) DEFAULT NULL COLLATE utf8_polish_ci,
                ADD toggl_error VARCHAR(255) DEFAULT NULL COLLATE utf8_polish_ci
        SQL);
    }
}
