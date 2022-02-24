<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180801133142 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("create table employee_sick_leave_request
(
  id          int auto_increment
    primary key,
  period_id   int      null,
  manager_id  int      null,
  employee_id int      null,
  comment     text     null,
  accepted_at datetime null,
  status      int      not null,
  created_at  datetime not null,
  updated_at  datetime null
)
  collate = utf8_unicode_ci;

create index IDX_95A9364D783E3463
  on employee_sick_leave_request (manager_id);

create index IDX_95A9364D8C03F15C
  on employee_sick_leave_request (employee_id);

create index IDX_95A9364DEC8B7ADE
  on employee_sick_leave_request (period_id)");

        $this->addSql("create table employee_sick_leave_period
(
  id                     int auto_increment
    primary key,
  employee_id            int          null,
  date_from              varchar(255) not null,
  date_to                varchar(255) not null,
  sick_leave_days_repeat int          not null,
  sick_leave_days        int          not null,
  daily_increase         double       not null,
  repeating              int          not null,
  comment_system         text         null,
  created_at             datetime     not null,
  updated_at             datetime     not null
)
  collate = utf8_unicode_ci;

create index IDX_2A6B86268C03F15C
  on employee_sick_leave_period (employee_id);");
        $this->addSql("create table employee_sick_leave_request_day
(
  id          int auto_increment
    primary key,
  request_id  int          null,
  period_id   int          null,
  employee_id int          null,
  manager_id  int          null,
  date        varchar(255) not null,
  type        int          not null,
  status      int          not null
)
  collate = utf8_unicode_ci;

create index IDX_1EF349C3427EB8A5
  on employee_sick_leave_request_day (request_id);

create index IDX_1EF349C3783E3463
  on employee_sick_leave_request_day (manager_id);

create index IDX_1EF349C38C03F15C
  on employee_sick_leave_request_day (employee_id);

create index IDX_1EF349C3EC8B7ADE
  on employee_sick_leave_request_day (period_id);");

        $this->addSql("create table sick_leave_attachment
(
  id          int auto_increment
    primary key,
  employee_id int          null,
  path        varchar(100) not null,
  name        varchar(100) not null,
  request_id  int          null,
  constraint path_UNIQUE
  unique (path),
  constraint FK_BE47A3A58C03F15C
  foreign key (employee_id) references employee (id),
  constraint FK_BE47A3A5427EB8A5
  foreign key (request_id) references employee_sick_leave_request (id)
    on update cascade on delete cascade
)
  collate = utf8_unicode_ci;
create index FK_BE47A3A5427EB8A5
  on sick_leave_attachment (request_id);
create index IDX_BE47A3A58C03F15C
  on sick_leave_attachment (employee_id);");

        $this->addSql("create table sick_leave_request_attachment
(
  sick_leave_request_id    int not null,
  sick_leave_attachment_id int not null,
  primary key (sick_leave_request_id, sick_leave_attachment_id),
  constraint FK_A43F06408566C2BB
  foreign key (sick_leave_request_id) references employee_sick_leave_request (id)
    on delete cascade,
  constraint FK_A43F0640D460611C
  foreign key (sick_leave_attachment_id) references sick_leave_attachment (id)
    on delete cascade
)
  collate = utf8_unicode_ci;
create index IDX_A43F06408566C2BB
  on sick_leave_request_attachment (sick_leave_request_id);
create index IDX_A43F0640D460611C
  on sick_leave_request_attachment (sick_leave_attachment_id);");

        $this->addSql('ALTER TABLE employee_sick_leave_request_day ADD CONSTRAINT FK_1EF349C3427EB8A5 FOREIGN KEY (request_id) REFERENCES employee_sick_leave_request (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee_sick_leave_request_day ADD CONSTRAINT FK_1EF349C3EC8B7ADE FOREIGN KEY (period_id) REFERENCES employee_sick_leave_period (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee_sick_leave_request_day ADD CONSTRAINT FK_1EF349C38C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee_sick_leave_request_day ADD CONSTRAINT FK_1EF349C3783E3463 FOREIGN KEY (manager_id) REFERENCES employee (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE employee_sick_leave_request ADD CONSTRAINT FK_95A9364DEC8B7ADE FOREIGN KEY (period_id) REFERENCES employee_sick_leave_period (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee_sick_leave_request ADD CONSTRAINT FK_95A9364D783E3463 FOREIGN KEY (manager_id) REFERENCES employee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee_sick_leave_request ADD CONSTRAINT FK_95A9364D8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE sick_leave_request_attachment');
        $this->addSql('DROP TABLE employee_sick_leave_request_day');
        $this->addSql('DROP TABLE employee_sick_leave_period');
        $this->addSql('DROP TABLE employee_sick_leave_request');
        $this->addSql('DROP TABLE sick_leave_attachment');

    }
}
