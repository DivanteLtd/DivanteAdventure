<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190116091527 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE employee ADD position_id INT DEFAULT NULL, ADD tribe_id INT DEFAULT NULL, ADD department_id INT DEFAULT NULL');

        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1DD842E46 FOREIGN KEY (position_id) REFERENCES position (id)');
        $this->addSql('CREATE INDEX IDX_5D9F75A1DD842E46 ON employee (position_id)');
        $this->addSql('UPDATE employee JOIN employee_position ep ON employee.id = ep.employee_id JOIN position p ON ep.position_id = p.id SET employee.position_id = p.id');
        $this->addSql('DROP TABLE employee_position');

        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A16F3EE0AD FOREIGN KEY (tribe_id) REFERENCES tribe (id)');
        $this->addSql('CREATE INDEX IDX_5D9F75A16F3EE0AD ON employee (tribe_id)');
        $this->addSql('UPDATE employee JOIN employee_tribe et ON employee.id = et.employee_id JOIN tribe t ON et.tribe_id = t.id SET employee.tribe_id = t.id');
        $this->addSql('DROP TABLE employee_tribe');

        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('CREATE INDEX IDX_5D9F75A1AE80F5DF ON employee (department_id)');
        $this->addSql('UPDATE employee JOIN employee_department ed ON employee.id = ed.employee_id JOIN department d ON ed.department_id = d.id SET employee.department_id = d.id');
        $this->addSql('DROP TABLE employee_department');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE employee_position(id int auto_increment primary key, employee_id int not null, position_id int not null, created_at  datetime default CURRENT_TIMESTAMP not null, updated_at  datetime default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP, constraint employee_id unique (employee_id), constraint FK_employee_position_employee foreign key (employee_id) references employee (id) on update cascade on delete cascade, constraint FK_employee_position_position foreign key (position_id) references position (id) on update cascade on delete cascade) charset = utf8');
        $this->addSql('CREATE TABLE employee_tribe(id int auto_increment primary key, employee_id int not null, tribe_id int not null, role varchar(255) null, created_at datetime default CURRENT_TIMESTAMP not null, updated_at datetime default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP, constraint employee_id_tribe_id unique (employee_id, tribe_id), constraint FK_employee_tribe_employee foreign key (employee_id) references employee (id) on update cascade on delete cascade, constraint FK_employee_tribe_tribe foreign key (tribe_id) references tribe (id) on update cascade on delete cascade) charset = utf8');
        $this->addSql('CREATE TABLE employee_department(id int auto_increment primary key, employee_id int not null, department_id int not null, created_at datetime default CURRENT_TIMESTAMP not null, updated_at datetime default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP, constraint employee_id_department_id unique (employee_id, department_id), constraint FK_employee_department_department foreign key (department_id) references department (id) on update cascade on delete cascade, constraint FK_employee_department_employee foreign key (employee_id) references employee (id) on update cascade on delete cascade)charset = utf8');

        $this->addSql('INSERT INTO employee_position(employee_id, position_id) SELECT id, position_id FROM employee WHERE position_id IS NOT NULL');
        $this->addSql('INSERT INTO employee_tribe(employee_id, employee_tribe) SELECT id, tribe_id FROM employee WHERE tribe_id IS NOT NULL');
        $this->addSql('INSERT INTO employee_department(employee_id, department_id) SELECT id, department_id FROM employee WHERE department_id IS NOT NULL');

        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1DD842E46');
        $this->addSql('DROP INDEX IDX_5D9F75A1DD842E46 ON employee');
        $this->addSql('DROP INDEX IDX_5D9F75A16F3EE0AD ON employee');
        $this->addSql('DROP INDEX IDX_5D9F75A1AE80F5DF ON employee');
        $this->addSql('ALTER TABLE employee DROP position_id, DROP tribe_id, DROP department_id');
    }
}
