<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190104065212 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("TRUNCATE TABLE `employee_occupancy`");
        $this->addSql("
            ALTER TABLE `employee_occupancy`
                DROP `updated_at`,
                DROP `created_at`,
                CHANGE `date` `date` int not null,
                CHANGE `occupancy` `occupancy` int not null
        ");
    }

    public function down(Schema $schema) : void
    {}
}
