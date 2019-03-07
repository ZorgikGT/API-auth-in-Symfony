<?php

namespace App\Controller;

use App\Entity\User;
use App\Managers\UserManager;
use App\Security\Authenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AuthController.
 */
class AuthController extends AbstractController
{
    /** @var Authenticator $authenticator */
    private $authenticator;

    /** @var UserManager $um */
    private $um;

    public function __construct(
        Authenticator $authenticator,
        UserManager $um
    )
    {
        $this->authenticator = $authenticator;
        $this->um = $um;
    }

    /**
     * @Route("/", name="index")
     * @Route("/{route}", name="vue_pages", requirements={"route"="^(?!.*api|_profiler|telegram).+"})
     */
    public function index()
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/api/login", name="login", methods={"POST"})
     */
    public function auth(Request $request)
    {
        $json = $request->getContent();
        $data = json_decode($json, true);

        if (!is_array($data)) {
            return new JsonResponse(['message' => 'Syntax error'], JsonResponse::HTTP_BAD_REQUEST);
        }
        if (empty($data['userName'])) {
            return new JsonResponse(['message' => 'userName should not be blank'], JsonResponse::HTTP_BAD_REQUEST);
        }
        if (!empty($error)) {
            return new JsonResponse(['message' => $error], JsonResponse::HTTP_BAD_REQUEST);
        }
        if (empty($data['password'])) {
            return new JsonResponse(['message' => 'password should not be blank'], JsonResponse::HTTP_BAD_REQUEST);
        }
//        if (!is_string($data['password']) && strlen($data['password']) > 255) {
//            return new JsonResponse(['message' => 'password should not be less than 255 chars'], JsonResponse::HTTP_BAD_REQUEST);
//        }

        /** @var User $user */
        $user = $this->um->getUserByUserName($data['userName']);
        if (!$user) {
            return new JsonResponse(
                ['message' => 'Username is invalid'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
        if ($data['password'] != $user->getPassword()) {
            return new JsonResponse(
                ['message' => 'Invalid password'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        $response = $this->authenticator->authorize($user);

        return $response;
    }

    /**
     * @Route("/api/logout", name="logout", methods={"POST"})
     */
    public function logout(Request $request)
    {
        $authCookie = $request->cookies->get(Authenticator::COOKIE_AUTH_NAME);

        $response = $this->authenticator->logout($authCookie);

        return $response;
    }
}
