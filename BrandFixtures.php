<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class BrandFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $brand = new Brand;
            $brand->setName("Brand $i");
            $brand->setDescription("Local Brand $i");
            $brand->setImage("62248af082334.png");
            $manager->persist($brand);
        }
        $manager->flush();
    }
}
