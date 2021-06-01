<?php


namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use App\Repository\GroupRepository;
use Doctrine\Common\Cache\Psr6\InvalidArgument;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class GroupsController extends AbstractController
{
    private GroupRepository $repository;
    private EntityManagerInterface $manager;

    public function __construct(
        GroupRepository $repository,
        EntityManagerInterface $manager
    ) {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * @Route("/admin/groups", name="groups")
     */
    public function groups(): Response
    {
        $groups = $this->repository->findAll();

        return $this->render('admin/groups.html.twig', ['groups' => $groups]);
    }
}
