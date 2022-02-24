<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200220103351 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql('ALTER TABLE hardware_agreement CHANGE `generated` is_generated TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE hardware_agreement ADD pesel blob, ADD nip blob, ADD company blob, ADD headquarters blob, ADD password_hashed TINYTEXT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql('ALTER TABLE hardware_agreement DROP pesel, DROP nip, DROP company, DROP headquarters, DROP password_hashed');
        $this->addSql('ALTER TABLE hardware_agreement CHANGE is_generated `generated` TINYINT(1) DEFAULT \'0\' NOT NULL');
    }
}
