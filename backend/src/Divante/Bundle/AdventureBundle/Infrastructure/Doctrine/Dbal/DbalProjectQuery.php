<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 16.01.19
 * Time: 12:22
 */

namespace Divante\Bundle\AdventureBundle\Infrastructure\Doctrine\Dbal;

use Divante\Bundle\AdventureBundle\Query\Project\ProjectQuery;
use Divante\Bundle\AdventureBundle\Query\Project\ProjectView;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Field;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Parser;
use Doctrine\DBAL\Connection;

class DbalProjectQuery implements ProjectQuery
{
    /** @var Field[]|null */
    private static ?array $fields = null;

    protected Connection $conn;
    protected Parser $parser;

    public function __construct(Connection $conn, Parser $parser)
    {
        $this->conn = $conn;
        $this->parser = $parser;
    }

    /** @return Field[] */
    protected function getFields() : array
    {
        if (is_null(self::$fields)) {
            self::$fields = [
                new Field("projectName", 'project', 'name'),
            ];
        }
        return self::$fields;
    }

    /** @inheritDoc */
    public function getAll(): array
    {
        $projects = $this->conn->fetchAll(
            'SELECT id, name, started_at, ended_at, archived FROM project'
        );
        return array_map(function ($project) {
            return new ProjectView(
                $project['id'],
                $project['name'],
                $project['started_at'],
                $project['ended_at'],
                $project['archived']
            );
        }, $projects);
    }

    /** @inheritDoc */
    public function getByQuery(string $query = ''): array
    {
        $sql = 'SELECT id, name, started_at, ended_at, archived FROM project';
        if (!empty($query)) {
            $query = $this->parser->parse($query, $this->getFields());
            if (!empty($query)) {
                $sql = $sql . ' WHERE '. $query;
            }
        }
        $projects = $this->conn->fetchAll($sql);
        return array_map(function ($project) {
            return new ProjectView(
                $project['id'],
                $project['name'],
                $project['started_at'],
                $project['ended_at'],
                $project['archived']
            );
        }, $projects);
    }
}
