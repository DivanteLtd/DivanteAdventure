<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 13.05.19
 * Time: 11:55
 */

namespace Divante\Bundle\AdventureBundle\Message\Dashboard;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreateNotification
{
    use ObjectTrait;
    private int $employeeId;
    private string $description;
    private string $subject;
    private string $path;

    public function __construct(int $employeeId, string $description, string $subject, string $path)
    {
        $this->employeeId = $employeeId;
        $this->description = $description;
        $this->subject = $subject;
        $this->path = $path;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
