<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\FAQ;

use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory;
use Divante\Bundle\AdventureBundle\Message\FAQ\UpdateFAQCategory;
use Doctrine\ORM\EntityManagerInterface;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateFAQCategoryHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param UpdateFAQCategory $message
     * @throws Exception
     */
    public function __invoke(UpdateFAQCategory $message) : void
    {
        $this->em->beginTransaction();
        /** @var FAQCategory|null $fAQCategory */
        $fAQCategory = $this->em->getRepository(FAQCategory::class)->find($message->getId());
        if (is_null($fAQCategory)) {
            throw new NotFoundHttpException("FAQ category with id $fAQCategory not found");
        }
        $fAQCategory->getEmployee()->clear();
        $employees = $message->getEmployees();
        try {
            $fAQCategory
                ->setNamePl($message->getNamePl())
                ->setNameEn($message->getNameEn())
                ->setUpdatedAt();
            /** @var int $employeeId */
            foreach ($employees as $employeeId) {
                /** @var Employee|null $employee */
                $employee = $this->em->getRepository(Employee::class)->find($employeeId);
                if (is_null($employee)) {
                    throw new NotFoundHttpException("Employee with ID $employeeId not found");
                }
                $fAQCategory->getEmployee()->add($employee);
            }
            $this->em->flush();
            $this->em->commit();
        } catch (Exception $e) {
            $this->em->rollback();
            throw new Exception($e);
        }
    }
}
