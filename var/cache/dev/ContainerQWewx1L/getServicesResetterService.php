<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the public 'services_resetter' shared service.

return $this->services['services_resetter'] = new \Symfony\Component\HttpKernel\DependencyInjection\ServicesResetter(new RewindableGenerator(function () {
    if (isset($this->privates['http_exception_listener'])) {
        yield 'http_exception_listener' => ($this->privates['http_exception_listener'] ?? null);
    }
    if (isset($this->privates['debug.stopwatch'])) {
        yield 'debug.stopwatch' => ($this->privates['debug.stopwatch'] ?? null);
    }
}, function () {
    return 0 + (int) (isset($this->privates['http_exception_listener'])) + (int) (isset($this->privates['debug.stopwatch']));
}), ['http_exception_listener' => 'reset', 'debug.stopwatch' => 'reset']);
