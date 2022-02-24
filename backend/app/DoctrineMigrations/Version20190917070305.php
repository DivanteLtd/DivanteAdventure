<?php

namespace Adventure\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20190917070305 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $records = $this->connection->fetchAll('SELECT e.hired_at, e.hired_to, e.tribe_id, t.name FROM employee e
INNER JOIN tribe t on e.tribe_id = t.id where e.tribe_id is not null and e.hired_at is not null;');
        foreach ($records as $record) {
            $date = new \DateTime($record['hired_at']);
            $r = $this->connection->fetchAll('SELECT id, number_of_enter, number_of_leave, number_of_work from tribe_rotation_history where tribe_name = :tribeName and year = :year and month = :month',
            ['tribeName' => $record['name'], 'month' => $date->format('m'), 'year'=> $date->format('Y')]);
            if (!isset($r[0])) {
                $this->connection->insert('tribe_rotation_history', [
                    'tribe_name' => $record['name'],
                    'number_of_enter' => 1,
                    'number_of_leave' => 0,
                    'number_of_work' => 1,
                    'number_of_female' => 0,
                    'number_of_male' => 0,
                    'month' => $date->format('m'),
                    'year' => $date->format('Y'),
                    'employees' => '[]',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            } else {
                $this->connection->update('tribe_rotation_history',
                    [
                        'number_of_enter' => $r[0]['number_of_enter'] + 1,
                        'number_of_work' => $r[0]['number_of_work'] + 1
                    ], ['id' => $r[0]['id']]);
            }
            if (!is_null($record['hired_to'])) {
                $date = new \DateTime($record['hired_to']);
                $r = $this->connection->fetchAll('SELECT id, number_of_enter, number_of_leave, number_of_work from tribe_rotation_history where tribe_name = :tribeName and year = :year and month = :month',
                    ['tribeName' => $record['name'], 'month' => $date->format('m'), 'year'=> $date->format('Y')]);
                if (!isset($r[0])) {
                    $this->connection->insert('tribe_rotation_history', [
                        'tribe_name' => $record['name'],
                        'number_of_enter' => 0,
                        'number_of_leave' => 1,
                        'number_of_work' => 0,
                        'number_of_female' => 0,
                        'number_of_male' => 0,
                        'month' => $date->format('m'),
                        'year' => $date->format('Y'),
                        'employees' => '[]',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                } else {
                    $this->connection->update('tribe_rotation_history',
                        [
                            'number_of_work' => $r[0]['number_of_work'] - 1,
                            'number_of_leave ' => $r[0]['number_of_leave'] + 1
                        ], ['id' => $r[0]['id']]);
                }


            }
        }
    }

    public function down(Schema $schema) : void
    {


    }
}
