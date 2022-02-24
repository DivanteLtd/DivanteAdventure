<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * News
 *
 * @ORM\Table(name="links")
 * @ORM\Entity
 */
class Link
{
    use Timestampable, Id;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private string $title;

    /**
     * @var string
     * @ORM\Column(name="url", type="text", length=65535, nullable=false)
     */
    private string $url;

    /**
     * @var Employee
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private Employee $author;

    public function getTitle() : string
    {
        return $this->title;
    }

    public function setTitle(string $title) : self
    {
        $this->title = $title;
        return $this;
    }

    public function getUrl() : string
    {
        return $this->url;
    }

    public function setUrl(string $url) : self
    {
        $this->url = $url;
        return $this;
    }

    public function getAuthor() : Employee
    {
        return $this->author;
    }

    public function setAuthor(Employee $author) : self
    {
        $this->author = $author;
        return $this;
    }
}
