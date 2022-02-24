<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 16.01.19
 * Time: 12:17
 */

namespace Divante\Bundle\AdventureBundle\Query\Project;

interface ProjectQuery
{
    /** @return ProjectView[] */
    public function getAll() :array;
    /**
     * @param string $query
     * @return ProjectView[]
     */
    public function getByQuery(string $query = ''): array;
}
