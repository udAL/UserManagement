<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class UsersController extends AbstractController
{
    /**
     * @Route("/admin/users", name="users")
     */
    public function users(ManagerRegistry $registry): Response
    {
        $users = $registry->getRepository(User::class)->findAll();

        return $this->render('admin/users.html.twig', ['users' => $users]);
    }
}
