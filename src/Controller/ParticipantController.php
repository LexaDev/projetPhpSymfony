<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ParticipantController extends AbstractController
{

    /**
     * @Route("/updateProfile",name="participant_update")
     * @param EntityManagerInterface $em
     * @param Request $request
     *
     */
    public function update(EntityManagerInterface $em,
                           Request $request,
                           UserPasswordEncoderInterface $passwordEncoder,
                           SluggerInterface $slugger)
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
            $imageFile = $participantForm->get('image')->getData();

            //Gestion de l'ajout d'une image
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                //Déplacer l'image dans le dossier correspondant
                try {
                    //Si cette utilisateur à déjà une image associé -> la supprimer
                    if($participant->getImageFilename() != null){
                        unlink($this->getParameter('images_profile_directory').'/'.$participant->getImageFilename());
                    }
                    $imageFile->move(
                        $this->getParameter('images_profile_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Impossible d\'ajouter cette image');
                }

                // Ajouter le nom de l'image à ce participant pour faire le lien et non le contenu de l'image
                $participant->setImageFilename($newFilename);
            }

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

    /**
     * @Route("/detailParticipant/{id}", name="participant_detail", requirements={"id": "\d+"})
     */
    public function detailParticipant($id, ParticipantRepository $repository)
    {
        $participant = $repository->find($id);
        if ($participant){
            return $this->render('participant/detail.html.twig', [
                'participant' => $participant
            ]);
        } else {
            $this->addFlash('warning', 'Participant inconnu !');
            return $this->redirectToRoute('home');
        }
    }

}
