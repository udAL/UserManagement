<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // Admin user
        $user = new User();
        $user->setEmail('eudald.rossell.vivo@gmail.com');
        $user->setName('udAL');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'secret'
        ));
        $user->setRoles(array('ROLE_ADMIN'));
        $manager->persist($user);

        // Normal user 1
        $user = new User();
        $user->setEmail('user1@gmail.com');
        $user->setName('user1');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'secret1'
        ));
        $user->setRoles(array('ROLE_USER'));
        $manager->persist($user);

        // Normal user 2
        $user = new User();
        $user->setEmail('user2@gmail.com');
        $user->setName('user2');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'secret2'
        ));
        $user->setRoles(array('ROLE_USER'));
        $manager->persist($user);

        $manager->flush();
    }
}
