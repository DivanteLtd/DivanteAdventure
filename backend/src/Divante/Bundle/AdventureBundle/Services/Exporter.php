<?php


namespace Divante\Bundle\AdventureBundle\Services;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Infrastructure\Doctrine\Dbal\DbalEmployeeQuery;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Column;

class Exporter
{
    private Reader $reader;
    private DbalEmployeeQuery $dbalEmployeeQuery;
    private CSVWriter $CSVWriter;
    private EntityManagerInterface $entityManager;

    public function __construct(
        Reader $reader,
        DbalEmployeeQuery $dbalEmployeeQuery,
        CSVWriter $CSVWriter,
        EntityManagerInterface $manager
    ) {
        $this->reader = $reader;
        $this->dbalEmployeeQuery = $dbalEmployeeQuery;
        $this->CSVWriter = $CSVWriter;
        $this->entityManager = $manager;
    }

    public function export() :string
    {
        $refClass = new \ReflectionClass(Employee::class);
        $refProps = $refClass->getProperties();
        $columns = [];
        $decorators = [];
        foreach ($refProps as $refProp) {
            /** @var \Divante\Bundle\AdventureBundle\Annotation\Exporter $e */
            $e = $this->reader->getPropertyAnnotation(
                $refProp,
                \Divante\Bundle\AdventureBundle\Annotation\Exporter::class
            );
            if (!empty($e)) {
                $columns[$e->columnName] = $e->humanName;
            }
            if (isset($e->decoratorClass)) {
                $decorators[$e->humanName] = $e->decoratorClass;
            }
        }
        $result = $this->dbalEmployeeQuery->getAllForListByFields($columns);
        $rows = [];
        $columnsName = [];
        if (!empty($result)) {
            $columnsName = array_keys($result[0]);
            foreach ($result as $value) {
                $rows[] = array_values($value);
            }
        }
        foreach ($decorators as $k => $decorator) {
            $key = array_search($k, $columnsName);
            $decorators[$key] = $decorator;
            unset($decorators[$k]);
        }
        foreach ($decorators as $k => $decorator) {
            foreach ($rows as $j => $row) {
                if (array_key_exists($k, $row)) {
                    $obj = new $decorator($this->entityManager);
                    $row[$k] = $obj->decorate($row[$k]);
                    $rows[$j] = $row;
                }
            }
        }
        return  $this->CSVWriter->write($columnsName, $rows);
    }
}
