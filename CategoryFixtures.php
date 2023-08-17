<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Validator\Constraints\DateTime;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i=1; $i<=10; $i++) {
            $category = new Category;
            $category->setName("category $i");
            $category->setDescription("Laptop");
            $category->setImage("622480d2997fc.png");


            $manager->persist($category);
        }

        $manager->flush();
    }
}
