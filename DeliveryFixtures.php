<?php

namespace App\DataFixtures;

use App\Entity\Delivery;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class DeliveryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $delivery = new Delivery;
            $delivery->setName("Grab Delivery $i");
            $delivery->setImage("622480d2997fc.png");
            $delivery->setDescription("Grab Holdings Limited operates a transportation and fintech platform in Southeast Asia.");
            $delivery->setImage("Grab_Logo_2021.jpeg");
            $manager->persist($delivery);
        }
        $manager->flush();
    }
}
