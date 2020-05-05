<?php


namespace App\DataFixtures;


use App\Entity\Site;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SiteFixture extends \Doctrine\Bundle\FixturesBundle\Fixture
{
    public const NB_SITE = 8;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {

        $siteList = ['La Roche-sur-Yon','Nantes','Angers','Rennes','Niort','Quimper','Le Mans','Laval'];
        $num=1;
        foreach ($siteList as $name) {
            $site = new Site();

            $site->setName($name);
            $this->addReference(Site::class.'_'.$num,$site);
            $manager->persist($site);
            $num++;
        }
        $manager->flush();
    }


}