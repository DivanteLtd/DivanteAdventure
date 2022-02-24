<?php

namespace Adventure\Migrations;

use Divante\Bundle\AdventureBundle\Entity\PublicHoliday;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200415090445 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql(<<<SQL
            CREATE TABLE public_holiday (
                id INT AUTO_INCREMENT NOT NULL,
                date DATE DEFAULT NULL,
                calculation_type INT DEFAULT NULL,
                name VARCHAR(255) NOT NULL,
                repeating TINYINT(1) NOT NULL,
                enabled TINYINT(1) NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB
        SQL);
        $defaultEnabled = [
            PublicHoliday::TYPE_CHRISTIAN_EASTER => "christian.easter",
            PublicHoliday::TYPE_CHRISTIAN_EASTER_MONDAY => "christian.easter_monday",
            PublicHoliday::TYPE_CHRISTIAN_CORPUS_CHRISTI => "christian.corpus_christi",
            PublicHoliday::TYPE_CHRISTIAN_PENTECOST => "christian.pentecost",
        ];
        $defaultDisabled = [
            PublicHoliday::TYPE_CHRISTIAN_GOOD_FRIDAY => "christian.good_friday",
            PublicHoliday::TYPE_CHRISTIAN_ASCENSION_DAY => "christian.ascension_day",
            PublicHoliday::TYPE_CHRISTIAN_PENTECOST_MONDAY => "christian.pentecost_monday",
            PublicHoliday::TYPE_US_MARTIN_LUTHER_KING_JR_BIRTHDAY => "us.martin_luther_king_jr_birthday",
            PublicHoliday::TYPE_US_GEORGE_WASHINGTON_BIRTHDAY => "us.george_washington_birthday",
            PublicHoliday::TYPE_US_MEMORIAL_DAY => "us.memorial_day",
            PublicHoliday::TYPE_US_LABOR_DAY => "us.labor_day",
            PublicHoliday::TYPE_US_COLUMBUS_DAY => "us.columbus_day",
            PublicHoliday::TYPE_US_THANKSGIVING_DAY => "us.thanksgiving_day",
        ];

        foreach ($defaultEnabled as $type => $name) {
            $this->addSql(<<<SQL
                INSERT INTO public_holiday
                    (`calculation_type`, `name`, `repeating`, `enabled`, `created_at`, `updated_at`)
                VALUES ('$type', '$name', 1, 1, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())
            SQL);
        }
        foreach ($defaultDisabled as $type => $name) {
            $this->addSql(<<<SQL
                INSERT INTO public_holiday
                    (`calculation_type`, `name`, `repeating`, `enabled`, `created_at`, `updated_at`)
                VALUES ('$type', '$name', 1, 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())
            SQL);
        }
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $this->addSql('DROP TABLE public_holiday');
    }
}
