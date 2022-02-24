<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 18.03.19
 * Time: 13:04
 */

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class AttachmentTemporaryToken
 * @package Divante\Bundle\AdventureBundle\Entity
 * @ORM\Table(name="sick_leave_attachment_download_token")
 * @ORM\Entity
 */
class AttachmentTemporaryToken
{
    use Id;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var SickLeaveAttachment
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\SickLeaveAttachment")
     * @ORM\JoinColumn(name="attachment_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $attachment;

    /**
     * @var string
     * @ORM\Column(name="token", type="string", length=64)
     */
    private $token;

    public function __construct(SickLeaveAttachment $attachment)
    {
        $this->attachment = $attachment;
        $prefix = mt_rand();
        $this->token = hash('sha256', uniqid("$prefix", true));
        $this->createdAt = new \DateTime();
    }

    public function getCreatedAt() : \DateTime
    {
        return $this->createdAt;
    }

    public function getAttachment() : SickLeaveAttachment
    {
        return $this->attachment;
    }

    public function getToken() : string
    {
        return $this->token;
    }
}
