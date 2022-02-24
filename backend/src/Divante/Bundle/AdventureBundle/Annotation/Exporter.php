<?php


namespace Divante\Bundle\AdventureBundle\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY","ANNOTATION"})
 */
final class Exporter
{
    /** @var string  */
    public string $columnName;
    /** @var string  */
    public string $humanName;
    /** @var bool  */
    public bool $export = false;
    /** @var string  */
    public string $decoratorClass;
}
