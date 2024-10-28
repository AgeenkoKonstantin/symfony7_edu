<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    

    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $users = [];

        $user = new User();
        $user->setEmail('admin@yandex.ru');
        $user->setRoles(['ROLE_ADMIN']);
        $password = $this->hasher->hashPassword($user, 'admin@yandex.ru');
        $user->setPassword($password);

        $manager->persist($user);

        for($i = 0; $i < 20; $i++) {

             $user = new User();
             $user->setEmail('user' . $i . '@yandex.ru');
             $password = $this->hasher->hashPassword($user, 'pass_1234');
             $user->setPassword($password);

             $manager->persist($user);

             $users[] = $user;
        }


        for($i = 0; $i < 100; $i++) {
            shuffle($users);
            foreach ($users as $user) {
                $blog = (new Blog($user))
                    ->setTitle('title' . $i)
                    ->setDescription('description' . $i)
                    ->setText('text' . $i);

                $manager->persist($blog);
            }
        }


        $manager->flush();
    }
}
