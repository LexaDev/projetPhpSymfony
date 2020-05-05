<?php


namespace App\DataFixtures;


use App\Entity\Location;
use App\Entity\Outing;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\State;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OutingFixture extends \Doctrine\Bundle\FixturesBundle\Fixture implements DependentFixtureInterface
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {

        $faker=Factory::create('fr_FR');

        for ($i =0;$i<5;$i++) {
            $outing = new Outing();

            $outing->setName($faker->catchPhrase);
            $date = $faker->dateTimeBetween('now + 3 days', 'now + 2 months');
            $outing->setDateLimitSigningUp($date);
            $outing->setNbSigningUpMax($faker->numberBetween(5, 10));
            $outing->setDateTimeStart($faker->dateTimeBetween('now + 3 months', 'now + 6 months'));
            $outing->setInfosOuting('Les infos sur la sortie');
            $outing->setDuration($faker->numberBetween(300, 360));

            //site
            $alea = rand(1, SiteFixture::NB_SITE);
            $outing->setSite($this->getReference(Site::class . '_' . $alea));


            //organizer
            $alea1 = rand(1, ParticipantFixture::NB_PARTICIPANT);
            $outing->setOrganizer($this->getReference(Participant::class . '_' . $alea1));



            //state 75 premier état Créée et les autres à publiée
            if ($i < 2) {
                $outing->setState($this->getReference(State::class . '_' . '1'));
            } else {
                $outing->setState($this->getReference(State::class . '_' . '2'));
            }

            $alea4 = rand(1, LocationFixture::NB_LOCATION);
            $outing->setLocation($this->getReference(Location::class . '_' . $alea4));


            $manager->persist($outing);
        }
        //state 3 cloturée
        for ($i =0;$i<4;$i++) {
            $outing = new Outing();

            $outing->setName($faker->catchPhrase);
            $date = $faker->dateTimeBetween('now - 2 month', 'now - 30 days');
            $outing->setDateLimitSigningUp($date);
            $outing->setNbSigningUpMax($faker->numberBetween(5, 10));
            $outing->setDateTimeStart($faker->dateTimeBetween('now - 29 days','now - 1 days'));
            $outing->setInfosOuting('Les infos sur la sortie');
            $outing->setDuration($faker->numberBetween(300, 360));

            //site
            $alea5 = rand(1,SiteFixture::NB_SITE);
            $outing->setSite($this->getReference(Site::class.'_'.$alea5));


            //organizer
            $alea6 = rand(1,ParticipantFixture::NB_PARTICIPANT);
            $outing->setOrganizer($this->getReference(Participant::class.'_'.$alea6));

            //participants exclure organizer

            for ($i = 0 ; $i< 3;$i++)
            {
                $alea8 = rand(1,ParticipantFixture::NB_PARTICIPANT);
                if ($alea8!=$alea6)
                {
                    $outing->addParticipant($this->getReference(Participant::class.'_'.$alea8));
                }
            }

            //state 3 cloturée
            $outing->setState($this->getReference(State::class.'_'.'3'));
            $alea9 =rand(1,LocationFixture::NB_LOCATION);
            $outing->setLocation($this->getReference(Location::class.'_'.$alea9));

            $manager->persist($outing);

        }

            $manager->flush();

    }

    public function getDependencies()
    {
        return [ParticipantFixture::class,
            CityFixture::class,
            SiteFixture::class,
            StateFixture::class,
            LocationFixture::class
            ];
    }

//    public function createManyState3(ObjectManager $manager)
//    {
//        $faker=Factory::create('fr_FR');
//
//        for ($i =0;$i<50;$i++) {
//            $outing = new Outing();
//
//            $outing->setName($faker->catchPhrase);
//            $date = $faker->dateTimeBetween('now - 2 month', 'now - 30 days');
//            $outing->setDateLimitSigningUp($date);
//            $outing->setNbSigningUpMax($faker->numberBetween(5, 30));
//            $outing->setDateTimeStart($faker->dateTimeBetween('now - 29 days','now - 1 days'));
//            $outing->setInfosOuting($faker->realText(200, 2));
//            $outing->setDuration($faker->numberBetween(60, 360));
//
//            //site
//            $alea = rand(1,SiteFixture::NB_SITE);
//            $outing->setSite($this->getReference(Site::class.'_'.$alea));
//
//
//            //organizer
//            $alea = rand(1,ParticipantFixture::NB_PARTICIPANT);
//            $outing->setOrganizer($this->getReference(Participant::class.'_'.$alea));
//
//            //participants exclure organizer
//            $alea = rand(0,$outing->getNbSigningUpMax());
//            for ($i = 0 ; $i< $alea;$i++)
//            {
//                $alea2 = rand(1,ParticipantFixture::NB_PARTICIPANT);
//                if ($alea2!=$alea)
//                {
//                    $outing->addParticipant($this->getReference(Participant::class.'_'.$alea2));
//                }
//            }
//
//            //state 3 cloturée
//
//            $outing->setState($this->getReference(State::class.'_'.'3'));
//
//
//            $alea =rand(1,LocationFixture::NB_LOCATION);
//            $outing->setLocation($this->getReference(Location::class.'_'.$alea));
//
//
//
//
//            $manager->persist($outing);
//
//        }
//    }
//
//    public function createManyState1And2(ObjectManager $manager)
//    {
//        $faker=Factory::create('fr_FR');
//
//        for ($i =0;$i<5;$i++) {
//            $outing = new Outing();
//
//            $outing->setName($faker->catchPhrase);
//            $date = $faker->dateTimeBetween('now + 3 days', 'now + 2 months');
//            $outing->setDateLimitSigningUp($date);
//            $outing->setNbSigningUpMax($faker->numberBetween(5, 30));
//            $outing->setDateTimeStart($faker->dateTimeBetween('now + 3 months','now + 6 months'));
//            $outing->setInfosOuting($faker->realText(200, 2));
//            $outing->setDuration($faker->numberBetween(60, 360));
//
//            //site
//            $alea = rand(1,SiteFixture::NB_SITE);
//            $outing->setSite($this->getReference(Site::class.'_'.$alea));
//
//
//            //organizer
//            $alea = rand(1,ParticipantFixture::NB_PARTICIPANT);
//            $outing->setOrganizer($this->getReference(Participant::class.'_'.$alea));
//
//            //participants exclure organizer
//            $alea = rand(0,$outing->getNbSigningUpMax());
//            for ($i = 0 ; $i< $alea;$i++)
//            {
//                $alea2 = rand(1,ParticipantFixture::NB_PARTICIPANT);
//                if ($alea2!=$alea)
//                {
//                    $outing->addParticipant($this->getReference(Participant::class.'_'.$alea2));
//                }
//            }
//
//            //state 75 premier état Créée et les autres à publiée
//            if ($i<75)
//            {
//                $outing->setState($this->getReference(State::class.'_'.'1'));
//            }else{
//                $outing->setState($this->getReference(State::class.'_'.'2'));
//            }
//
//            $alea =rand(1,LocationFixture::NB_LOCATION);
//            $outing->setLocation($this->getReference(Location::class.'_'.$alea));
//
//
//
//
//            $manager->persist($outing);
//
//        }
//    }



}