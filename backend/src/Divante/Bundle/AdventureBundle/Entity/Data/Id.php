<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 28.12.18
 * Time: 11:42
 */

namespace Divante\Bundle\AdventureBundle\Entity\Data;

use Doctrine\ORM\Mapping as ORM;

trait Id
{

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    public function getId() : int
    {
        return $this->id;
    }
}
