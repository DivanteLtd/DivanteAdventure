<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200323112415 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE potential_employee ADD private_phone VARCHAR(50) NOT NULL, ADD remote TINYINT(1) NOT NULL, ADD city VARCHAR(50) DEFAULT NULL, ADD postal_code VARCHAR(50) DEFAULT NULL, ADD street VARCHAR(100) DEFAULT NULL, ADD recruiter_id INT DEFAULT NULL, ADD private_email VARCHAR(50) NOT NULL, ADD source VARCHAR(50) DEFAULT NULL, ADD trip TINYINT(1) NOT NULL, ADD company VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE potential_employee ADD CONSTRAINT FK_26DD2863156BE243 FOREIGN KEY (recruiter_id) REFERENCES employee (id)');
        $this->addSql('CREATE INDEX IDX_26DD2863156BE243 ON potential_employee (recruiter_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE potential_employee DROP private_phone, DROP remote, DROP city, DROP postal_code, DROP street, DROP recruiter_id, DROP private_email, DROP source, DROP trip, DROP company');
        $this->addSql('ALTER TABLE potential_employee DROP FOREIGN KEY FK_26DD2863156BE243');
        $this->addSql('DROP INDEX IDX_26DD2863156BE243 ON potential_employee');
    }
}
