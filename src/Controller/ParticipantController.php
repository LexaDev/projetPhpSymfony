<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class ParticipantController extends AbstractController
{
    /**
     * @Route("/register", name="participant_register")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param PasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(EntityManagerInterface $em, Request $request, PasswordEncoderInterface $passwordEncoder)
    {
        $participant = new Participant();
        $participantForm = $this->createForm(ParticipantType::class,$participant);

        $participantForm->handleRequest($request);
        if ($participantForm->isSubmitted() && $participantForm->isValid())
        {
            $participant->setActif(true);
            $participant->setRoles(['ROLE_USER']);
            $password = $passwordEncoder->encodePassword($participant,$participant->getPassword());
            $participant->setPassword($password);

            $em->persist($participant);
            $em->flush();

            $this->addFlash('success','Inscription réussie');
            return  $this->redirectToRoute('home');

        }


        return $this->render('participant/register.html.twig',[
            'partiForm'=>$participantForm->createView()
        ]);
    }
}
