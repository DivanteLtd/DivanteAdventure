<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Controller\Api\EvidenceController;
use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class EvidenceInvoice
 * @package Divante\Bundle\AdventureBundle\Entity
 * @ORM\Table(name="evidence_invoice_attachment", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="path", columns={"path"})
 * })
 * @ORM\Entity @ORM\HasLifecycleCallbacks
 */
class EvidenceInvoice
{
    use Id, Timestampable;

    /**
     * @var string
     * @ORM\Column(name="path", type="string", length=100)
     */
    private $path;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var UploadedFile|null
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {
     *         "application/msword",
     *         "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
     *         "application/pdf",
     *         "image/png",
     *         "image/jpg",
     *         "image/jpeg",
     *         "image/bmp"
     * },
     *     maxSizeMessage = "The maximum allowed file size is 5MB.",
     *     mimeTypesMessage = "Only the .doc, .docx, .pdf, .png, .jpg, .jpeg are allowed"
     * )
     */
    private $file;

    /**
     * @var Evidence|null
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Evidence", inversedBy="invoices")
     * @ORM\JoinColumn(name="evidence_id", referencedColumnName="id", onDelete="cascade")
     */
    private $evidence;

    /**
     * @var Employee
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="owner_employee_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $owner;

    public function setPath(string $path) : self
    {
        $this->path = $path;
        return $this;
    }

    public function getPath() : string
    {
        return $this->path;
    }

    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setFile(?UploadedFile $file) : self
    {
        $this->file = $file;
        return $this;
    }

    public function getFile() : ?UploadedFile
    {
        return $this->file;
    }

    public function setEvidence(?Evidence $evidence) : self
    {
        $this->evidence = $evidence;
        if (!is_null($evidence) && !in_array($this, $evidence->getInvoices())) {
            $evidence->addInvoice($this);
        }
        return $this;
    }

    public function getEvidence() : ?Evidence
    {
        return $this->evidence;
    }

    public function setOwner(Employee $owner) : self
    {
        $this->owner = $owner;
        return $this;
    }

    public function getOwner() : Employee
    {
        return $this->owner;
    }

    /**
     * @ORM\PrePersist @ORM\PreUpdate
     */
    public function preUpload() : void
    {
        if (isset($this->file)) {
            $prefix = mt_rand();
            $this->path = sha1(uniqid("$prefix", true)).'.'.$this->file->getClientOriginalExtension();
        }
    }

    /**
     * @ORM\PreRemove
     */
    public function removeUpload() : void
    {
        $file = $this->getAbsolutePath();
        if (!is_null($file)) {
            unlink($file);
        }
    }

    /**
     * @ORM\PostPersist @ORM\PostUpdate
     */
    public function upload() : void
    {
        if (isset($this->file)) {
            $this->file->move($this->getUploadRootDir(), $this->path);
        }
    }

    private function getAbsolutePath() : ?string
    {
        if ($this->path === null) {
            return null;
        }
        return $this->getUploadRootDir().'/'.$this->path;
    }

    private function getUploadRootDir() : string
    {
        return __DIR__.'/../../../../../web/var/uploads/'.EvidenceController::EVIDENCE_INVOICE_DIRECTORY;
    }
}
