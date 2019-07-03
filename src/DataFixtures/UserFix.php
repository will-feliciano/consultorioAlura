<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFix extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('usuario');
        $user->setPassword('$argon2i$v=19$m=1024,t=2,p=2$dEg1SnFvYzFKV1VLOVF0WQ$+jCTZxKy3uK+Qx6yTLIWQ3ol/sdPq0JX3dTdhEuWbIU');
        $manager->persist($user);

        $manager->flush();
    }
}
