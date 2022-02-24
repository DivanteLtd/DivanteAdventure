<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 14.02.19
 * Time: 11:00
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Leaves;

use Divante\Bundle\AdventureBundle\Message\Leaves\UpdateRequestDay;
use Doctrine\ORM\EntityManagerInterface;

class UpdateRequestDayHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param UpdateRequestDay $message
     * @throws \Exception
     */
    public function __invoke(UpdateRequestDay $message) : void
    {
        $em = $this->em;
        $em->getConnection()->beginTransaction();
        try {
            $day = $message->getLeaveRequestDay();
            $day
                ->setStatus($message->getStatus())
                ->setType($message->getType());
            $em->persist($day);
            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw new \Exception("Updating leave request day failed", 0, $e);
        }
    }
}
