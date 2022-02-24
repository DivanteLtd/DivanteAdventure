<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 02.01.19
 * Time: 08:55
 */

namespace Divante\Bundle\AdventureBundle\Infrastructure\Messenger;

use Symfony\Component\Messenger\MessageBusInterface;

interface MessageBusCreatorInterface
{
    public function createMessageBus() : MessageBusInterface;
}
