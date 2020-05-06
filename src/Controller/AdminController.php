<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/create",name="admin_create")
     * @param EntityManagerInterface $em
     * @param Request $request
     *
     */
    public function update(EntityManagerInterface $em,Request $request,UserPasswordEncoderInterface $passwordEncoder,SluggerInterface $slugger)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            //recupère user en cours
            $participant = new Participant();
            // creation du formulaire
            $participantForm = $this->createForm(UserType::class, $participant);

            //hydratation de participant via le formulaire
            $participantForm->handleRequest($request);

            if ($participantForm->isSubmitted() && $participantForm->isValid()) {
                //recuperation des child unmapped
                $firstPass = $participantForm->get('newPassword')->get('first')->getData();
                $secondPass = $participantForm->get('newPassword')->get('second')->getData();
                $imageFile = $participantForm->get('image')->getData();
                $role = $participantForm->get('roles')->getData();
                dump($role);
                //gestiondes roles
                if ($role == 1)
                {
                    $participant->setRoles(["ROLE_ADMIN"]);
                }else{
                    $participant->setRoles(["ROLE_USER"]);
                }
                //Gestion de l'ajout d'une image
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                    //Déplacer l'image dans le dossier correspondant
                    try {
                        //Si cette utilisateur à déjà une image associé -> la supprimer
                        if ($participant->getImageFilename() != null) {
                            unlink(
                                $this->getParameter('images_profile_directory') . '/' . $participant->getImageFilename()
                            );
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
                if (isset($firstPass) && isset($secondPass) && $firstPass === $secondPass) {
                    //deuxième controle pattern

                        $partiRepo = $this->getDoctrine()->getRepository(Participant::class);
                        //changement du password, effectuer le changement du password (uniquement) en base aussi
                        $partiRepo->upgradePassword(
                            $participant,
                            $passwordEncoder->encodePassword($participant, $firstPass)
                        );
                    }

                //modification en base
                $em->persist($participant);
                $em->flush();
                $this->addFlash('success', 'Ajout éffectué avec succès');

               // return $this->redirectToRoute('participant_detail', ['id' => $participant->getId()]);
            }
            return $this->render(
                'participant/createUser.html.twig',
                [
                    'partiForm' => $participantForm->createView()
                ]
            );
       }else{
            return $this->redirectToRoute('home');
        }
        }

}
