<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Cache\Psr6\InvalidArgument;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class UsersController extends AbstractController
{
    private UserRepository $repository;
    private EntityManagerInterface $manager;
    private UserPasswordEncoderInterface $encoder;

    public function __construct(
        UserRepository $repository,
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $encoder)
    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/admin/users", name="users")
     */
    public function users(): Response
    {
        $users = $this->repository->findAll();

        return $this->render('admin/users.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/admin/user/new", name="user.new", methods={"GET"})
     */
    public function user_new(): Response
    {
        $user = new User();
        $available_roles = User::$available_roles;
        return $this->render('admin/user.html.twig', ['user' => $user, 'available_roles' => $available_roles, 'error' => '']);
    }

    /**
     * @Route("/admin/user/{id}", name="user.get", methods={"GET"})
     */
    public function user_get(int $id): Response
    {
        $user = $this->repository->find($id);
        $available_roles = User::$available_roles;
        if (!$user) {
            throw $this->createNotFoundException('User not found: '.$id);
        }
        else {
            return $this->render('admin/user.html.twig', ['user' => $user, 'available_roles' => $available_roles, 'error' => '']);
        }
    }

    /**
     * @Route("/admin/user/new", name="user.new.post", methods={"POST"})
     */
    public function user_new_post(Request $request): Response
    {
        $user = new User();
        $data = $request->request->all();
        $user_same_email = $this->repository->findBy(['email' => $data['email']]);
        if($user_same_email) {
            throw new InvalidArgument('User email already exists');
        }
        else {
            $user->setEmail($data['email']);
            $user->setPassword(
                $this->encoder->encodePassword($user, $data['password'])
            );
            $this->user_save($user, $data);

            return $this->redirectToRoute('users');
        }
    }

    /**
     * @Route("/admin/user/{id}", name="user.post", methods={"POST"})
     */
    public function user_post(int $id, Request $request): Response
    {
        $user = $this->repository->find($id);
        if (!$user) {
            throw $this->createNotFoundException(
                'User not found: '.$id
            );
        }
        else {
            $this->user_save($user, $request->request->all());
        }
        return $this->redirectToRoute('users');
    }

    private function user_save(User $user, array $data)
    {
        $user->setName($data['name']);

        $accepted_roles = array();
        if(!isset($data['roles']) || !is_array($data['roles'])) {
            $data['roles'] = array();
        }

        foreach($data['roles'] as $role)
        {
            if(in_array($role, User::$available_roles))
            {
                $accepted_roles[] = $role;
            }
        }
        $user->setRoles(array_unique($accepted_roles));
        $this->manager->persist($user);
        $this->manager->flush();
    }

    /**
     * @Route("/admin/user/delete/{id}", name="user.delete")
     */
    public function user_delete(int $id, Security $security): Response
    {
        $user = $this->repository->find($id);
        if (!$user) {
            throw $this->createNotFoundException(
                'User not found: '.$id
            );
        }
        else {
            if($user->getUsername() != $security->getUser()->getUsername()) {
                $this->manager->remove($user);
                $this->manager->flush();
            }
        }
        return $this->redirectToRoute('users');
    }
}
