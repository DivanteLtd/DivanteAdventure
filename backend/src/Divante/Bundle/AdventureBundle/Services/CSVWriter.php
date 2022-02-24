<?php


namespace Divante\Bundle\AdventureBundle\Services;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class CSVWriter
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function write(array $columns, array $rows) :string
    {
        $filesystem = new Filesystem();
        $varPath = $this->kernel->getProjectDir() . '/var';
        $fullPathDirectory = $varPath . '/lists';
        $token = time();
        if (!$filesystem->exists($fullPathDirectory)) {
            $filesystem->mkdir($fullPathDirectory);
        }
        $fp = fopen($fullPathDirectory . '/'. $token . '.csv', 'w');
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        foreach (array_merge([$columns], $rows) as $fields) {
            fputcsv($fp, $fields, ';');
        }

        fclose($fp);
        return (string)$token;
    }
}
