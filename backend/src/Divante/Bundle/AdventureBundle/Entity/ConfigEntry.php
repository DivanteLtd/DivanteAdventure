<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ConfigEntry
 * @package Divante\Bundle\AdventureBundle\Entity
 * @ORM\Table(name="config_entry")
 * @ORM\Entity(repositoryClass="Divante\Bundle\AdventureBundle\Entity\Repository\ConfigEntryRepository")
 */
class ConfigEntry
{
    use Id;

    /** @ORM\Column(name="config_key", type="string", length=256) */
    private string $key;
    /** @ORM\Column(name="config_value", type="string", length=2048) */
    private string $value;
    /** @ORM\Column(name="config_group", type="string", length=2048) */
    private string $group;
    /** @ORM\Column(name="created_at", type="datetime") */
    private DateTime $createdAt;
    /** @ORM\Column(name="replaced_at", type="datetime", nullable=true) */
    private ?DateTime $replacedAt = null;
    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee", fetch="EAGER")
     * @ORM\JoinColumn(name="responsible_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private ?Employee $responsible = null;

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): ConfigEntry
    {
        $this->key = $key;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): ConfigEntry
    {
        $this->value = $value;
        return $this;
    }

    public function setGroup(string $group): ConfigEntry
    {
        $this->group = $group;
        return $this;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getResponsible(): ?Employee
    {
        return $this->responsible;
    }

    public function setResponsible(?Employee $responsible): ConfigEntry
    {
        $this->responsible = $responsible;
        return $this;
    }

    public function setCreatedAt(): ConfigEntry
    {
        $this->createdAt = new DateTime();
        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setReplacedAt(): ConfigEntry
    {
        $this->replacedAt = new DateTime();
        return $this;
    }

    public function getReplacedAt(): ?DateTime
    {
        return $this->replacedAt;
    }
}
