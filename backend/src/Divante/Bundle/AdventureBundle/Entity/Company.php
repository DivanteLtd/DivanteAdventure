<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="company")
 * @ORM\Entity
 */
class Company
{
    use Id;

    /** @ORM\Column(name="name", type="string", length=255, nullable=false) */
    private string $name;

    /** @ORM\Column(name="address", type="string", length=65535, nullable=false) */
    private string $address;

    /** @ORM\Column(name="email_domain", type="string", length=65535, nullable=false) */
    private string $emailDomain;

    /** @ORM\Column(name="vat_id", type="string", length=20, nullable=false) */
    private string $vatId;


    public function getName() : string
    {
        return $this->name;
    }

    public function getAddress() : string
    {
        return $this->address;
    }

    public function getEmailDomain() : string
    {
        return $this->emailDomain;
    }

    public function getVatId() : string
    {
        return $this->vatId;
    }
}
