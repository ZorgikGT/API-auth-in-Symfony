<?php

namespace App\Security\Annotations\Drivers;

use App\Security\Authenticator;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use App\Security\Annotations\Cookie;
use App\Security\Managers\AccessManager;

class AnnotationDriver
{
    /** @var Reader $reader */
    private $reader;

    /** @var AccessManager $am */
    private $am;

    public function __construct(Reader $reader, AccessManager $am)
    {
        $this->reader = $reader;
        $this->am = $am;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if (!is_array($controller = $event->getController())) {
            return;
        }
        /** @var \ReflectionObject $object */
        $object = new \ReflectionObject($controller[0]);
        try {
            $method = $object->getMethod($controller[1]);
        } catch (\ReflectionException $reflectionException) {
            throw new $reflectionException;
        }
        $methodAnnotation = $this->reader->getMethodAnnotations($method);

        foreach ($methodAnnotation as $configuration) {

            if ($configuration instanceof Cookie) {

                if (Authenticator::COOKIE_AUTH_NAME === $configuration->cookie) {
                    try {
                        $user = $this->am->hasAccess($event->getRequest(), $configuration->roles);
                    } catch(AccessDeniedException $exception) {
                        throw $exception;
                    }

                    $event->getRequest()->attributes->set('user', $user);
                }
            }
        }
    }
}
