<?php

namespace App\Managers;



use App\Entity\User;
use App\Security\Annotations\Cookie;
use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    /** @var EntityManagerInterface $em */
    protected $em;

    /**
     * UserManager constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getUserByUserName(string $userName)
    {
        $user = $this->em->getRepository(User::class)
            ->findOneBy(array(
                'userName' => $userName
                ));

        return $user;
    }

    public function getUserById(int $id)
    {
        $user = $this->em->getRepository(User::class)->find($id);

        return $user;
    }
}
