<?php


namespace Divante\Bundle\AdventureBundle\Message;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreatePlannerReport
{
    use ObjectTrait;

    /** @var array<int|string,mixed> */
    private array $data;
    private string $filePath;

    /**
     * CreatePlannerReport constructor.
     * @param array<int|string,mixed> $data
     * @param string $filePath
     */
    public function __construct(array $data, string $filePath)
    {
        $this->data = $data;
        $this->filePath = $filePath;
    }

    /** @return array<int|string,mixed> */
    public function getData(): array
    {
        return $this->data;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }
}
