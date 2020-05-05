<?php


namespace App\DataFixtures;


use App\Entity\State;
use Doctrine\Persistence\ObjectManager;

class StateFixture extends \Doctrine\Bundle\FixturesBundle\Fixture
{
    public const NB_STATE=5;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {

        $listeStates = ['Créée','Publiée','Cloturée','Annulée','Archivée'];
        $num = 1;
        foreach ($listeStates as $etat)
        {

            $state = new State();
            $state->setLabel($etat);
            $this->addReference(State::class.'_'.$num,$state);
            $num++;
            $manager->persist($state);

        }
        $manager->flush();

    }
}