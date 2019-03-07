<?php

namespace App\Security;

use App\Entity\User;
use App\Managers\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class Authenticator
{
    const COOKIE_LIFETIME = 3600;

    const COOKIE_AUTH_NAME = 'X-AUTH-TOKEN';

    /** @var EntityManagerInterface $em */
    private $em;

    /** @var UserManager $um */
    private $um;

    /** @var SessionInterface $session */
    private $session;

    public function __construct(
        EntityManagerInterface $em,
        UserManager $um,
        SessionInterface $session
    ) {
        $this->em = $em;
        $this->um = $um;
        $this->session = $session;
    }

    /**
     * @param User $user
     *
     * @return JsonResponse
     */
    public function authorize(User $user)
    {
        $this->session->set($cookie = uniqid(), $user);
        $response = new JsonResponse(['message' => 'Successful login'], JsonResponse::HTTP_OK);

        $token = (new UserToken())
            ->setId($user->getId())
            ->setRole($user->getRole());

        $response->headers->setCookie(new Cookie(
                Authenticator::COOKIE_AUTH_NAME,
                $cookie,
                time() + Authenticator::COOKIE_LIFETIME,
                '/',
                null,
                false,
                true
            )
        );

        return $response;
    }

    /**
     * @param string $cookie
     *
     * @return User|UserToken
     */
    public function getUserByCookie(string $cookie)
    {
        /** @var UserToken $userToken */
        $userToken = $this->session->get($cookie);

        if(!empty($data)) {
            /** @var User $userToken */
            $userToken = $this->um->getUserById($userToken->getId());
        }

        return $userToken;
    }

    /**
     * @param string|null $cookie
     *
     * @return JsonResponse
     */
    public function logout(?string $cookie)
    {
        $response = new JsonResponse(['message' => 'Error! User not found'], JsonResponse::HTTP_FORBIDDEN);

        if (!empty($this->session->get($cookie))) {
            $this->session->remove($cookie);
            $response->headers->removeCookie(self::COOKIE_AUTH_NAME);

            $response->setStatusCode(JsonResponse::HTTP_OK);
            $response->setData(['message' => 'Successful logout!']);
        }

        return $response;
    }

}
