<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\FAQ;

use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory;
use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQQuestion;
use Divante\Bundle\AdventureBundle\Message\FAQ\CreateFAQQuestion;
use Doctrine\ORM\EntityManagerInterface;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateFAQQuestionHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param CreateFAQQuestion $message
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function __invoke(CreateFAQQuestion $message) : void
    {
        $this->em->beginTransaction();
        try {
            $employeeId = $message->getEmployeeId();
            /** @var Employee|null $employee */
            $employee = $this->em->getRepository(Employee::class)->find($employeeId);
            if (is_null($employee)) {
                throw new NotFoundHttpException(sprintf("Employee with ID %d not found", $employeeId));
            }
            $fAQCategoryId = $message->getCategoryId();
            /** @var FAQCategory|null $fAQCategory */
            $fAQCategory = $this->em->getRepository(FAQCategory::class)->find($fAQCategoryId);
            if (is_null($fAQCategory)) {
                throw new NotFoundHttpException(sprintf("FAQ category with ID %d not found", $fAQCategory));
            }
            if ($fAQCategory->getEmployee()->contains($employee)
                || $employee->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
                $fAQQuestion = (new FAQQuestion())
                    ->setEmployee($employee)
                    ->setFAQCategory($fAQCategory)
                    ->setQuestionPl($message->getQuestionPl())
                    ->setQuestionEn($message->getQuestionEn())
                    ->setAnswerPl($message->getAnswerPl())
                    ->setAnswerEn($message->getAnswerEn())
                    ->setCreatedAt()
                    ->setUpdatedAt();
                $this->em->persist($fAQQuestion);
                $this->em->flush();
                $this->em->commit();
            } else {
                throw new NotFoundHttpException(
                    "You are not responsible to create this question. Please contact with DA Team"
                );
            }
        } catch (Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
