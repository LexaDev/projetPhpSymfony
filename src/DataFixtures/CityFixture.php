<?php


namespace App\DataFixtures;


use App\Entity\City;
use Doctrine\Persistence\ObjectManager;

class CityFixture extends \Doctrine\Bundle\FixturesBundle\Fixture
{

    public const NB_CITY = 10;


    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $cityList = ['Nantes'=>'44000',
                    'Angers'=>'49000',
                    'La Roche-sur-Yon'=>'85000',
                    'Les Sables d\'Olonne'=>'85100',
                    'Quimper'=>'29000',
                    'Rennes'=>'35000',
                    'Niort'=>'79000',
                    'Le Mans'=>'72000',
                    'Brest'=>'29200',
                    'Paris'=>'75000'
        ];
        $index = 1;
        foreach ($cityList as $ville=>$zip)
        {
            $city = new City();
            $city->setName($ville);
            $city->setZip($zip);
            $this->setReference(City::class.'_'.$index,$city);
            $index++;
            $manager->persist($city);
        }
        $manager->flush();



    }
}