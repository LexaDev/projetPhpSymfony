<?php


namespace App\DataFixtures;


use App\Entity\City;
use App\Entity\Location;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LocationFixture extends \Doctrine\Bundle\FixturesBundle\Fixture implements DependentFixtureInterface
{

    public const NB_LOCATION =300;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $faker=Factory::create('fr_FR');
        for ($i=1;$i<301;$i++)
        {
            $location = new Location();
            $location->setName($faker->company);
            $location->setLongitude($faker->longitude);
            $location->setLatitude($faker->latitude);
            $location->setStreet($faker->streetName);

            $alea = rand(1,CityFixture::NB_CITY);
            $location->setCity($this->getReference(City::class.'_'.$alea));

            $this->addReference(Location::class.'_'.$i,$location);
            $manager->persist($location);
        }
        $manager->flush();

    }

    public function getDependencies()
    {
        return [CityFixture::class];
    }


}