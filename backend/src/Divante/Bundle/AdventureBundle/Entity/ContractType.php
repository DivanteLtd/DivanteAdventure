<?php


namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ContractType
 * @ORM\Table(name="contract_type")
 * @ORM\Entity
 */
class ContractType
{
    use Timestampable, Id;

    /** @ORM\Column(name="code", type="string", length=10, nullable=false) */
    private string $code;
    /** @ORM\Column(name="name", type="string", length=50, nullable=false) */
    private string $name;
    /** @ORM\Column(name="description", type="string", length=255) */
    private ?string $description;
    /** @ORM\Column(name="active", type="boolean") */
    private bool $active = true;

     /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }


    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }
}
