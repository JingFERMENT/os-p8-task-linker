<?php

namespace App\DataFixtures;

use App\Factory\EmployeeFactory;
use App\Factory\ProjectFactory;
use App\Factory\StatutFactory;
use App\Factory\TagFactory;
use App\Factory\TaskFactory;
use App\Factory\TimeslotFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        EmployeeFactory::createMany(5);
        ProjectFactory::createMany(5);
        StatutFactory::createMany(3);
        TagFactory::createMany(5);
        TaskFactory::createMany(5);
        TimeslotFactory::createMany(5); 
        $manager->flush();
    }
}
