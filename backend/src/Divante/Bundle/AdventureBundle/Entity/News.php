<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * News
 *
 * @ORM\Table(name="news")
 * @ORM\Entity
 */
class News
{
    use Timestampable, Id;

    public const TYPE_MARKDOWN = 0;
    public const TYPE_HTML = 1;

    /** @ORM\Column(name="title", type="string", length=255, nullable=true) */
    private ?string $title = null;
    /** @ORM\Column(name="banner", type="string", length=255, nullable=true) */
    private ?string $banner = null;
    /** @ORM\Column(name="description", type="text", length=16777215, nullable=false, columnDefinition="mediumblob") */
    private string $description;
    /** @ORM\Column(name="type", type="integer", nullable=false) */
    private int $type = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private Employee $author;

    public function getTitle() : ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title) : self
    {
        $this->title = $title;
        return $this;
    }

    public function getBanner() : ?string
    {
        return $this->banner;
    }

    public function setBanner(?string $banner) : self
    {
        $this->banner = $banner;
        return $this;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function setDescription(string $description) : self
    {
        $this->description = $description;
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

    public function getType() : int
    {
        return $this->type;
    }

    public function setType(int $type) : self
    {
        $this->type = $type;
        return $this;
    }
}
