<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 07.01.19
 * Time: 13:01
 */

namespace Divante\Bundle\AdventureBundle\Entity\Data;

/**
 * Interface SchedulerHideable is used for such entities that can be hidden from scheduler.
 * @package Divante\Bundle\AdventureBundle\Entity\Data
 */
interface SchedulerHideable
{
    /**
     * If that function returns `true`, it will be hidden from scheduler.
     * @return bool
     */
    public function isHiddenFromScheduler() : bool;
}
