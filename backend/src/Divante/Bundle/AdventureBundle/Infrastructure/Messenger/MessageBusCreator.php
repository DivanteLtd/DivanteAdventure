<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 02.01.19
 * Time: 08:56
 */

namespace Divante\Bundle\AdventureBundle\Infrastructure\Messenger;

use Divante\Bundle\AdventureBundle\Exception\InvalidHandlerException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

class MessageBusCreator implements MessageBusCreatorInterface
{
    /** @var array<string,array<string,mixed>> */
    private array $handlers = [];
    private ?MessageBusInterface $messageBus = null;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param object $handler
     * @throws InvalidHandlerException
     */
    public function addHandler($handler) : void
    {
        try {
            $class = new \ReflectionClass($handler);
            if (!$class->isInstantiable()) {
                return;
            }
            if (!$class->hasMethod('__invoke')) {
                $error = sprintf("Handler %s doesn't have __invoke method", $class->getName());
                throw new InvalidHandlerException($error, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $method = $class->getMethod('__invoke');
            $parameters = $method->getParameters();
            if (count($parameters) !== 1) {
                $error = sprintf(
                    "Method __invoke in handler %s should have exactly 1 parameter, %d declared",
                    $class->getName(),
                    count($parameters)
                );
                throw new InvalidHandlerException($error, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $parameter = $parameters[0];
            /** @var \ReflectionClass|null $type */
            $type = $parameter->getClass();
            if (is_null($type)) {
                $error = sprintf(
                    "Argument of method __invoke in handler %s doesn't have a type",
                    $class->getName()
                );
                throw new InvalidHandlerException($error, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            $messageClassName = $type->getName();
            $this->handlers[$messageClassName] = [ 'dummy' => $handler ];
        } catch (\ReflectionException $e) {
            $error = sprintf("Reflection exception in class %s", get_class($handler));
            throw new InvalidHandlerException($error, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createMessageBus(): MessageBusInterface
    {
        if (is_null($this->messageBus)) {
            $handlersLocator = new HandlersLocator($this->handlers);
            $handleMessageMiddleware = new HandleMessageMiddleware($handlersLocator);
            $this->messageBus = new MessageBuss($this->logger, [$handleMessageMiddleware]);
        }
        return $this->messageBus;
    }
}
