<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
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

        //recupère user en cours
        $participant = $this->getUser();
        // creation du formulaire
        $participantForm = $this->createForm(ParticipantType::class, $participant);

        //hydratation de participant via le formulaire
        $participantForm->handleRequest($request);


        if ($participantForm->isSubmitted() && $participantForm->isValid() ) {

            //recuperation des child unmapped
            $firstPass = $participantForm->get('newPassword')->get('first')->getData();
            $secondPass =$participantForm->get('newPassword')->get('second')->getData();

            //deuxième controle double saisie identique
            if (isset($firstPass) && isset($secondPass) && $firstPass===$secondPass) {
                //deuxième controle pattern
                preg_match('#(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}#', $firstPass, $matches);

                if (count($matches) === 0) {
                    $participantForm->get('newPassword')
                        ->addError(
                            new FormError('Le Mot de passe doit contenir au moins 8 caractères avec au moins une majuscule et un chiffre')
                            );
                }else{

                    $partiRepo = $this->getDoctrine()->getRepository(Participant::class);
                    //changement du password, effectuer le changement du password (uniquement) en base aussi
                    $partiRepo->upgradePassword($participant,$passwordEncoder->encodePassword($participant,$firstPass));

                }

            }
            //modification en base
            $em->persist($participant);
            $em->flush();
            $this->addFlash('success','Modifications éffectuées avec succès');

            return $this->redirectToRoute('participant_profile');
        }

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
