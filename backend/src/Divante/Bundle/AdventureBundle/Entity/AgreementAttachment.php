<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

/**
 * AgreementAttachment
 *
 * @ORM\Table(name="attachment", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="UNIQ_795FD9BB5E237E06", columns={"name"}),
 *     @ORM\UniqueConstraint(name="UNIQ_795FD9BBB548B0F", columns={"path"}),
 * })
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class AgreementAttachment
{
    use Id;

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
     * Image file
     * @var UploadedFile
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {
     *         "application/msword",
     *         "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
     *         "application/pdf"
     * },
     *     maxSizeMessage = "The maximum allowed file size is 5MB.",
     *     mimeTypesMessage = "Only the .doc, .docx, .pdf are allowed"
     * )
     */
    private $file;

    public function setPath(string $path) : self
    {
        $this->path = $path;
        return $this;
    }

    public function getPath() : string
    {
        return $this->path;
    }

    public function getFile() : File
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
            // target filename to move to
            $this->file->move(
                $this->getUploadRootDir(),
                $this->path
            );
        }
    }

    protected function getAbsolutePath() : ?string
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir() . '/' . $this->path;
    }

    protected function getWebPath() : ?string
    {
        return null === $this->path
            ? null
            : $this->getUploadDir() . '/' . $this->path;
    }

    protected function getUploadRootDir() : string
    {
        return __DIR__ . '/../../../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir() : string
    {
        return 'uploads/documents';
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name) : self
    {
        $this->name = $name;

        return $this;
    }
}
