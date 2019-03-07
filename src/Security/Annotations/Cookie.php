<?php

namespace App\Security\Annotations;

use App\Entity\User;

/**
 * @Annotation
 */

class Cookie
{
    /** @var string $cookie */
    public $cookie;

    /** @var array $roles */
    public $roles = [User::ROLE_USER, User::ROLE_ADMIN];
}
