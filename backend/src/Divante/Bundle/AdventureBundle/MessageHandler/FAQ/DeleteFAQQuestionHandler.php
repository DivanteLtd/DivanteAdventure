<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\FAQ;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory;
use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQQuestion;
use Divante\Bundle\AdventureBundle\Message\FAQ\DeleteFAQQuestion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteFAQQuestionHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeleteFAQQuestion $message
     * @throws \Exception
     * @throws NotFoundHttpException
     */
    public function __invoke(DeleteFAQQuestion $message) : void
    {
        $this->em->beginTransaction();
        $id = $message->getId();
        /** @var FAQQuestion|null $entry */
        $entry = $this->em->getRepository(FAQQuestion::class)->find($id);
        if (is_null($entry)) {
            throw new NotFoundHttpException("FAQ Question with id $id not found.");
        }
        $employeeId = $message->getEmployeeId();
        /** @var Employee|null $employee */
        $employee = $this->em->getRepository(Employee::class)->find($employeeId);
        if (is_null($employee)) {
            throw new NotFoundHttpException(sprintf("Employee with ID %d not found", $employeeId));
        }
        /** @var FAQCategory|null $fAQCategory */
        $fAQCategory = $entry->getFAQCategory();
        if (is_null($fAQCategory)) {
            throw new NotFoundHttpException(sprintf("FAQ category with ID %d not found", $fAQCategory));
        }
        if ($fAQCategory->getEmployee()->contains($employee)
            || $employee->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
            $this->em->remove($entry);
            $this->em->flush();
            $this->em->commit();
        } else {
            throw new NotFoundHttpException(sprintf(
                "You are not responsible to delete this question. Please contact with DA Team",
            ));
        }
    }
}
