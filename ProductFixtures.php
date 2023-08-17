<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i=1; $i<=100; $i++) {
            $product = new Product;
            $product->setName("product $i");
            $product->setImage("622480d2997fc.png");
            $product->setDescription("Apple's iPhone 13 features a ceramic shield front");
            $product->setPrice((float)rand(120,500));
            $product->setDate(\DateTime::createFromFormat('Y/m/d','2022/5/16'));
            $product->setQuantity(rand(12,100));

            $manager->persist($product);
        }

        $manager->flush();
    }
}
