<?php

namespace App\Controller;

use App\Managers\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Security\Annotations\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 */
class UserController extends AbstractController
{
    /** @var UserManager $um */
    private $um;

    public function __construct(UserManager $um)
    {
        $this->um = $um;
    }

    /**
     * @Route("/api/users/{id}", name="users.get.one", methods={"GET"}, requirements={"id": "\d+"})
     * @Cookie(cookie="X-AUTH-TOKEN", roles={["ROLE_ADMIN", "ROLE_USER"]})
     */
    public function getUserById(int $id)
    {
        $user = $this->um->getUserById($id);
        if (!$user) {
            return new JsonResponse(['message' => 'This user doesn\'t exist'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($user, JsonResponse::HTTP_OK, [], false);
    }


}
