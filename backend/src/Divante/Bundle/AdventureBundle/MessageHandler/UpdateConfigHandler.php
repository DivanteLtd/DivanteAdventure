<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler;

use Divante\Bundle\AdventureBundle\Entity\ConfigEntry;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\SystemConfig;
use Divante\Bundle\AdventureBundle\Message\UpdateConfig;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateConfigHandler
{
    private SystemConfig $config;
    private EntityManagerInterface $em;

    public function __construct(SystemConfig $config, EntityManagerInterface $em)
    {
        $this->config = $config;
        $this->em = $em;
    }

    public function __invoke(UpdateConfig $message): void
    {
        $employee = $this->getEmployee($message->getEmployeeId());
        /**
         * @var string $key
         * @var string $value
         */
        foreach ($message->getEntries() as $key => $value) {
            $this->update($key, $value, $employee);
        }
        $this->em->flush();
    }

    private function update(string $key, string $value, Employee $employee): void
    {
        $oldEntry = $this->config->getValue($key);
        if (!is_null($oldEntry)) {
            if ($oldEntry->getValue() === $value) {
                return; // value is the same and does not require updating
            }
            $oldEntry->setReplacedAt();
        }
        $newEntry = (new ConfigEntry())
            ->setKey($key)
            ->setValue($value)
            ->setGroup(explode(".", $key)[0])
            ->setResponsible($employee)
            ->setCreatedAt();
        $this->em->persist($newEntry);
    }

    private function getEmployee(int $id): Employee
    {
        /** @var Employee|null $employee */
        $employee = $this->em->getRepository(Employee::class)->find($id);
        if (is_null($employee)) {
            throw new NotFoundHttpException("Employee with ID $id not found");
        }
        return $employee;
    }
}
