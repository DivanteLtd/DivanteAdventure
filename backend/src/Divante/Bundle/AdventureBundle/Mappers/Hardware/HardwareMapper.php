<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Mappers\Hardware;

use Divante\Bundle\AdventureBundle\Entity\Hardware\HardwareAgreement;
use Divante\Bundle\AdventureBundle\Entity\Hardware\HardwareAssignment;
use Divante\Bundle\AdventureBundle\Mappers\Mapper;
use Doctrine\ORM\EntityManagerInterface;

class HardwareMapper implements Mapper
{
    const NOT_SIGNED = 'not signed';
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->em = $manager;
    }

    protected function getObjectManager() : EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @param int $id
     * @return array<int,array<string,mixed>>
     */
    public function mapEntity($id) : array
    {
        $hardwareList = $this->em->getRepository(HardwareAssignment::class)
            ->findBy(['employee' => $id]);
        $list = [];
        /** @var HardwareAssignment $hardware */
        foreach ($hardwareList as $hardware) {
            $hardwareId = $hardware->getId();
            $hardwareAgreement = $this->em->getRepository(HardwareAgreement::class)
                ->findBy(['assignment' => $hardwareId]);
            if ($hardwareAgreement && $hardware->isSendEmail()) {
                if ($hardwareAgreement[0]->getSignedByUser()) {
                    $agreementSigned = $hardwareAgreement[0]->getSignedByUser()->format('Y-m-d');
                } else {
                    $agreementSigned = self::NOT_SIGNED;
                }
            }
            array_push(
                $list,
                [
                    'category' => $hardware->getCategory(),
                    'manufacturer' => $hardware->getManufacturer(),
                    'model' => $hardware->getModel(),
                    'serialNumber' => $hardware->getSerialNumber(),
                    'agreementSigned' => $agreementSigned ?? null
                ]
            );
        }
        return $list;
    }
}
