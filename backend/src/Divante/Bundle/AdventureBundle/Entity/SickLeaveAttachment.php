<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class SickLeaveAttachment
 *
 * @ORM\Table(name="sick_leave_attachment", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="path", columns={"path"})
 * })
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class SickLeaveAttachment
{
    protected static string $staticUploadDir = 'var/uploads/sick-leave';

    use Id;

    /** @ORM\Column(name="path", type="string", length=100) */
    private string $path;
    /** @ORM\Column(name="name", type="string", length=100) */
    private string $name;
    /**
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
    private UploadedFile $file;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Employee")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Employee $employee;

    /**
     * @var Collection<int,LeaveRequest>
     * @ORM\ManyToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\LeaveRequest", mappedBy="attachments")
     */
    private Collection $requests;

    public function __construct()
    {
        $this->requests = new ArrayCollection();
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file) : self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Called before saving the entity
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() : void
    {
        if (isset($this->file)) {
            $prefix = mt_rand();
            $this->path = sha1(uniqid("$prefix", true)) . '.' . $this->file->getClientOriginalExtension();
        }
    }

    /**
     * Called before entity removal
     *
     * @ORM\PreRemove()
     */
    public function removeUpload() : void
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    /**
     * Called after entity persistence
     *
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() : void
    {
        // target filename to move to
        if (isset($this->file)) {
            $this->file->move(
                $this->getUploadRootDir(),
                $this->path
            );
        }
    }

    protected function getAbsolutePath(): string
    {
        return $this->getUploadRootDir() . '/' . $this->path;
    }

    protected function getWebPath(): string
    {
        return $this->getUploadDir() . '/' . $this->path;
    }

    protected function getUploadRootDir(): string
    {
        return __DIR__ . '/../../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir(): string
    {
        return self::$staticUploadDir;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(Employee $employee): self
    {
        $this->employee = $employee;
        return $this;
    }

    public function addRequest(LeaveRequest $request) : SickLeaveAttachment
    {
        if (!$this->requests->contains($request)) {
            $this->requests->add($request);
        }
        return $this;
    }
}
