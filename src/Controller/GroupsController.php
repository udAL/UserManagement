<?php


namespace App\Controller;

use App\Entity\Group;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupsController extends AbstractController
{
    private GroupRepository $group_repository;
    private UserRepository $user_repository;
    private EntityManagerInterface $manager;

    public function __construct(
        GroupRepository $group_repository,
        UserRepository $user_repository,
        EntityManagerInterface $manager
    ) {
        $this->group_repository = $group_repository;
        $this->user_repository = $user_repository;
        $this->manager = $manager;
    }

    /**
     * @Route("/admin/groups", name="groups")
     */
    public function groups(): Response
    {
        $groups = $this->group_repository->findAll();

        return $this->render('admin/groups.html.twig', ['groups' => $groups]);
    }

    /**
     * @Route("/admin/group/new", name="group.new", methods={"GET"})
     */
    public function group_new(): Response
    {
        $group = new Group();
        $users = $this->user_repository->findAll();
        return $this->render(
            'admin/group.html.twig',
            ['group' => $group, 'users' => $users, 'error' => '']
        );
    }

    /**
     * @Route("/admin/group/{id}", name="group.get", methods={"GET"})
     */
    public function group_get(int $id): Response
    {
        $group = $this->group_repository->find($id);
        if (!$group) {
            throw $this->createNotFoundException('Group not found: ' . $id);
        } else {
            $users = $this->user_repository->findAll();
            return $this->render(
                'admin/group.html.twig',
                ['group' => $group, 'users' => $users, 'error' => '']
            );
        }
    }

    /**
     * @Route("/admin/group/new", name="group.new.post", methods={"POST"})
     */
    public function group_new_post(Request $request): Response
    {
        $group = new Group();
        $this->group_save($group, $request->request->all());

        return $this->redirectToRoute('groups');
    }

    /**
     * @Route("/admin/group/{id}", name="group.post", methods={"POST"})
     */
    public function group_post(int $id, Request $request): Response
    {
        $group = $this->group_repository->find($id);
        if (!$group) {
            throw $this->createNotFoundException(
                'Group not found: ' . $id
            );
        } else {
            $this->group_save($group, $request->request->all());
        }
        return $this->redirectToRoute('groups');
    }

    private function group_save(Group $group, array $data)
    {
        $users = $group->getUsers();

        if(!isset($data['users']) || !is_array($data['users'])) {
            $data['users'] = array();
        }

        foreach($users as $user) {
            if( !in_array($user->getId(), $data['users']) ) {
                $group->removeUser($user);
            }
            else {
                unset($data['users'][array_search($user->getId(), $data['users'])]);
            }
        }
        foreach($data['users'] as $user_id) {
            $user = $this->user_repository->find($user_id);
            if($user) {
                $group->addUser($user);
            }
        }

        $this->manager->persist($group);
        $this->manager->flush();
    }

    /**
     * @Route("/admin/group/delete/{id}", name="group.delete")
     */
    public function group_delete(int $id): Response
    {
        $group = $this->group_repository->find($id);
        if (!$group) {
            throw $this->createNotFoundException(
                'Group not found: ' . $id
            );
        } else {
            if( sizeof($group->getUsers()) ) {
                throw new \Exception('Group is not empty: ' . $id);
            }
            else {
                $this->manager->remove($group);
                $this->manager->flush();
            }
        }
        return $this->redirectToRoute('groups');
    }
}
