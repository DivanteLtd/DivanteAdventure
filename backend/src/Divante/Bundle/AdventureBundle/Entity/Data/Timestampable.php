<?php
/**
 * Created by PhpStorm.
 * User: norbert
 * Date: 24.10.18
 * Time: 06:44
 */
namespace Divante\Bundle\AdventureBundle\Entity\Data;

/**
 * Trait Timestampable handles two common database fields: 'created_at' and 'updated_at'. These two fields are now in
 * this trait with getters and setters.
 *
 * Usage: add 'use Timestampable;" at the beginning of your class.
 * @package Divante\Bundle\AdventureBundle\Entity\Data
 */
trait Timestampable
{
    /**
     * Created at
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * Updated at
     * @var \DateTime
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    protected $updatedAt;

    public function getCreatedAt() : \DateTime
    {
        return $this->createdAt;
    }

    /** @return $this */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
        return $this;
    }

    public function getUpdatedAt() : \DateTime
    {
        return $this->updatedAt;
    }

    /** @return $this */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
        return $this;
    }
}
