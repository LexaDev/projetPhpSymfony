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
     *
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

    /**
     * @Route("/updateProfile",name="participant_update")
     * @param EntityManagerInterface $em
     * @param Request $request
     *
     */
    public function update(EntityManagerInterface $em, Request $request, PasswordEncoderInterface $passwordEncoder)
    {
        $participant = $this->getDoctrine()->getRepository(Participant::class)->find(1);
        $encodePassword = $this->getUser()->getPassword();
        $participantForm = $this->createForm(ParticipantType::class, $participant);

        $participantForm->handleRequest($request);
        if ($participantForm->isSubmitted() && $participantForm->isValid()) {

            $password = $request->get('password');
             if($passwordEncoder->isPasswordValid($encodePassword,$password))

                 $participant->setPassword($encodePassword);
                 $em->persist($participant);
                 $em->flush();

                 $this->addFlash('success', 'Inscription réussie');
                 return $this->redirectToRoute('home');
        }

        return  $this->render('participant/updateProfil.html.twig',[
            'partiForm'=> $participantForm->createView()
        ]);
    }

}
