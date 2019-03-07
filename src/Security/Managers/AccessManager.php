<?php

namespace App\Security\Managers;

use App\Security\Authenticator;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

class AccessManager
{
    private $authenticator;

    public function __construct(Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    public function hasAccess(Request $request, array $roles)
    {
        $cookie = $request->cookies->get(Authenticator::COOKIE_AUTH_NAME);

        if (!$cookie) {
            throw new AccessDeniedException('Unauthorized.');
        }
        if (!$user = $this->authenticator->getUserByCookie($cookie)) {
            throw new AccessDeniedException('Unauthorized.');
        }
        if (!in_array($user->getRole(), $roles)) {
            throw new AccessDeniedException('Access denied.');
        }

        return $user;
    }

}