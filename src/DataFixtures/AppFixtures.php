<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private Generator $faker;
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ){
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $user = (new User)
            ->setEmail('admin@test.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setFirstName('Morgan')
            ->setLastName('Nawrot')
            ->setPassword($this->hasher->hashPassword( new User, 'Test1234'));

            $manager->persist($user);

        for($i = 0; $i < 10; $i++){
            $user = (new User)
            ->setEmail($this->faker->unique()->email())
            ->setFirstName( $this->faker->firstName())
            ->setLastName( $this->faker->lastName())
            ->setRoles( $this->faker->randomElements(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_EDITOR']))
            ->setPassword($this->hasher->hashPassword( new User, 'Test1234'));

            $manager->persist($user);
        }

        $manager->flush();
    }
}
