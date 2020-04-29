<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ParticipantController extends AbstractController
{

    /**
     * @Route("/updateProfile",name="participant_update")
     * @param EntityManagerInterface $em
     * @param Request $request
     *
     */
    public function update(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {

        $participant = new Participant();
        $participantForm = $this->createForm(ParticipantType::class, $participant);

        if ($request->get('participant[password][second]'))


        return  $this->render('participant/updateProfil.html.twig',[
            'partiForm'=> $participantForm->createView()
        ]);
    }

    /**
     * @Route("/profile",name="participant_profile")
     */
    public function profile()
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            return $this->render('participant/profile.html.twig');
        }
    }
}
