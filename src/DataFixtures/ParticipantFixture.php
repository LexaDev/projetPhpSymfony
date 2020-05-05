<?php


namespace App\DataFixtures;

require_once 'vendor/autoload.php';
use App\Entity\Participant;
use App\Entity\Site;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ParticipantFixture extends \Doctrine\Bundle\FixturesBundle\Fixture implements DependentFixtureInterface
{
    private $encoder;
    private $index=1;
    public const NB_PARTICIPANT = 50;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder=$encoder;

    }


    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $faker=Factory::create('fr_FR');

        $adminList=['Lucas'=>'Rambaud',
            'Alexandra'=>'Peyrical',
            'Alban'=>'Uzureau'];
        //user admin
       foreach ($adminList as $firstName=>$lastName) {

            $participant = new Participant();

            $participant->setPassword($this->encoder->encodePassword($participant, 'admin'));
            $participant->setUsername($firstName);
            $email = strtolower($firstName).'.'.strtolower($lastName).'2019@campus-eni.fr';
            $participant->setEmail($email);
            $participant->setPhoneNumber($faker->phoneNumber);
            $participant->setFirstName($firstName);
            $participant->setLastName($lastName);
            $participant->setActif(1);

            $this->addReference(Participant::class.'_'.$this->index,$participant);

            $participant->setRoles(['ROLE_ADMIN']);
            $alea = rand(1,SiteFixture::NB_SITE);
            $participant->setSite($this->getReference(Site::class.'_'.$alea));

            $manager->persist($participant);
            $this->index=$this->index+1;
        }



        //users basique
        for ($i=0;$i<47;$i++) {

            $participant = new Participant();

            $participant->setPassword($this->encoder->encodePassword($participant, 'password'));
            $participant->setUsername($faker->unique()->userName);
            $participant->setEmail($faker->unique()->freeEmail);
            $participant->setPhoneNumber($faker->phoneNumber);
            $participant->setFirstName($faker->firstName());
            $participant->setLastName($faker->lastName);
            $participant->setActif(1);
            $participant->setRoles(['ROLE_USER']);

            $this->addReference(Participant::class.'_'.$this->index,$participant);

            $alea = rand(1,SiteFixture::NB_SITE);
            $participant->setSite($this->getReference(Site::class.'_'.$alea));

            $manager->persist($participant);
            $this->index=$this->index+1;
        }
        $manager->flush();

    }

    public function getDependencies()
    {
        return [SiteFixture::class];
    }
}