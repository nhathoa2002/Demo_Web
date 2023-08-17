<?php

namespace App\DataFixtures;

use App\Entity\Supplier;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class SupplierFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $supplier = new Supplier;
            $supplier->setName("Supplier $i");
            $supplier->setDescription(" U.S. Gaming Supplies Inc. is located in Coral Springs, FL, United States and is part of the Miscellaneous Durable Goods Merchant Wholesalers Industry. U.S");
            $supplier->setImage("package.jpeg");
            $manager->persist($supplier);
        }
        $manager->flush();
    }
}
