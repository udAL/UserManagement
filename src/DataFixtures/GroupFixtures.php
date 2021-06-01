<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Group;

class GroupFixtures extends Fixture
{
    public const GROUP1 = 'group1';
    public const GROUP2 = 'group2';
    public const GROUP3 = 'group3';

    public function load(ObjectManager $manager)
    {
        // Group 1
        $group = new Group();
        $manager->persist($group);
        $this->addReference(self::GROUP1, $group);

        // Group 2
        $group = new Group();
        $manager->persist($group);
        $this->addReference(self::GROUP2, $group);

        // Group 3
        $group = new Group();
        $manager->persist($group);
        $this->addReference(self::GROUP3, $group);

        $manager->flush();
    }
}
