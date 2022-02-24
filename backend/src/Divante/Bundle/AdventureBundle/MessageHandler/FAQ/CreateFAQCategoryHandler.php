<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\FAQ;

use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory;
use Divante\Bundle\AdventureBundle\Message\FAQ\CreateFAQCategory;
use Doctrine\ORM\EntityManagerInterface;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateFAQCategoryHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param CreateFAQCategory $message
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function __invoke(CreateFAQCategory $message) : void
    {
        $this->em->beginTransaction();
        $employees = $message->getEmployees();
        try {
            $fAQCategory = (new FAQCategory())
                ->setNamePl($message->getNamePl())
                ->setNameEn($message->getNameEn())
                ->setCreatedAt()
                ->setUpdatedAt();
            $this->em->persist($fAQCategory);
            /** @var int $employeeId */
            foreach ($employees as $employeeId) {
                /** @var Employee|null $employee */
                $employee = $this->em->getRepository(Employee::class)->find($employeeId);
                if (is_null($employee)) {
                    throw new NotFoundHttpException(sprintf("Employee with ID %d not found", $employeeId));
                }
                $fAQCategory->getEmployee()->add($employee);
            }
            $this->em->flush();
            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
