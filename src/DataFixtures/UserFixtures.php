<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class UserFixtures extends Fixture
{
    
    public function  __construct(private UserPasswordHasherInterface $passwordEncoder)
    {
        
    }


    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($usr = 0; $usr < 4; $usr++ ){
            $user = new User();
            $user->setEmail($faker->email())
                ->setPassword($this->passwordEncoder->hashPassword($user,'ArethiA1975!'))
                ->setRoles(mt_rand(0,1) ===1 ? ['ROLE_USER']: ['ROLE_USER','ROLE_REDACTOR'])
                ->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($user);
        }

        $manager->flush();
        
    }
}
