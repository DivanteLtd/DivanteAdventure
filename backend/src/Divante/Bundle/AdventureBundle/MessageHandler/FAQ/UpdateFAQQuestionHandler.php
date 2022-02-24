<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\FAQ;

use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory;
use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQQuestion;
use Doctrine\ORM\EntityManagerInterface;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Divante\Bundle\AdventureBundle\Message\FAQ\UpdateFAQQuestion;

class UpdateFAQQuestionHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param UpdateFAQQuestion $message
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function __invoke(UpdateFAQQuestion $message): void
    {
        try {
            $this->em->beginTransaction();
            $fAQQuestionId = $message->getId();
            /** @var FAQQuestion|null $fAQQuestion */
            $fAQQuestion = $this->em->getRepository(FAQQuestion::class)->find($fAQQuestionId);
            if (is_null($fAQQuestion)) {
                throw new NotFoundHttpException("FAQ category with id $fAQQuestionId not found");
            }
            $employeeId = $message->getEmployeeId();
            /** @var Employee|null $employee */
            $employee = $this->em->getRepository(Employee::class)->find($employeeId);
            if (is_null($employee)) {
                throw new NotFoundHttpException("Employee with id $employeeId not found");
            }
            $fAQCategoryId = $message->getCategoryId();
            /** @var FAQCategory|null $fAQCategory */
            $fAQCategory = $this->em->getRepository(FAQCategory::class)->find($fAQCategoryId);
            if (is_null($fAQCategory)) {
                throw new NotFoundHttpException("FAQ category with id $fAQCategory not found");
            }
            if ($fAQCategory->getEmployee()->contains($employee)
                || $employee->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
                $fAQQuestion
                    ->setEmployee($employee)
                    ->setFAQCategory($fAQCategory)
                    ->setQuestionPl($message->getQuestionPl())
                    ->setQuestionEn($message->getQuestionEn())
                    ->setAnswerPl($message->getAnswerPl())
                    ->setAnswerEn($message->getAnswerEn())
                    ->setUpdatedAt();
                $this->em->flush();
                $this->em->commit();
            } else {
                throw new NotFoundHttpException(sprintf(
                    "You are not responsible to update this question. Please contact with DA Team",
                ));
            }
        } catch (Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
