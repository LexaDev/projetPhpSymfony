<?php

namespace App\Controller;


use App\Entity\Location;
use App\Entity\Outing;
use App\Entity\State;
use App\Form\OutingType;
use App\Repository\OutingRepository;
use App\Repository\StateRepository;
use Cassandra\Date;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OutingController extends AbstractController
{
    /**
     * @Route("/createOuting", name="create_outing")
     */
    public function createOuting(EntityManagerInterface $em, Request $request)
    {
        $outing = new Outing();

        $outingForm = $this->createForm(OutingType::class, $outing);

        $outingForm->handleRequest($request);
        if ($outingForm->isSubmitted() && $outingForm->isValid())
        {
            //Chargement de l'état
            if ($request->get('save')){
                $stateRepo = $this->getDoctrine()->getRepository(State::class);
                $outing->setState($stateRepo->find(1));
            } elseif ($request->get('publish')){
                $stateRepo = $this->getDoctrine()->getRepository(State::class);
                $outing->setState($stateRepo->find(2));
            }

            //gestion du datetime input séparé controle saisie
            $dateStart = $outingForm->get('dateStart')->getData();
            $timeStart = $outingForm->get('timeStart')->getData();
            if (isset($dateStart) && isset($timeStart)) {
                $hour = $timeStart->format('H');
                $min = $timeStart->format('i');
                $dateStart->setTime($hour, $min);
                $outing->setDateTimeStart($dateStart);


                if ($outing->getDateTimeStart()>$outing->getDateLimitSigningUp()) {
                    //Chargement de l'organizer
                    $outing->setOrganizer($this->getUser());
                    //Chargement du site
                    $outing->setSite($this->getUser()->getSite());
            //Chargement de l'organizer
            $outing->setOrganizer($this->getUser());
            //Chargement du site
            $outing->setSite($this->getUser()->getSite());

                    $em->persist($outing);
                    $em->flush();

                    return $this->redirectToRoute(
                        'view_outing',
                        [
                            "id" => $outing->getId(),
                        ]
                    );
                }else{
                   $outingForm->get('dateStart')->addError(new FormError('La sortie doit se dérouler après la date limite d\'inscription'));
                   $outingForm->get('timeStart')->addError(new FormError('La sortie doit se dérouler après la date limite d\'inscription'));
                }
            }

        }
        return $this->render(
            'outing/manageOuting.html.twig',
            [
                'outingForm' => $outingForm->createView(),
                'cardTitle' => 'Créer',
                'btnSuppr' => false,
            ]
        );
    }
    /**
     * @Route("/updateOuting/{id}", name="update_outing", requirements={"id":"\d+"})
     */
    public function updateOuting(EntityManagerInterface $em, Request $request,$id)
    {
        $outingRepo = $em->getRepository(Outing::class);
        $outing = $outingRepo->find($id);
        $updateForm = $this->createForm(OutingType::class,$outing);
        $updateForm->handleRequest($request);
        if ($updateForm->isSubmitted() && $updateForm->isValid())
        {
            //Chargement de l'état
            if ($request->get('save')){
                $stateRepo = $this->getDoctrine()->getRepository(State::class);
                $outing->setState($stateRepo->find(1));
            } elseif ($request->get('publish')){
                $stateRepo = $this->getDoctrine()->getRepository(State::class);
                $outing->setState($stateRepo->find(2));
            }

            $em->flush();
            $this->addFlash('success','Modifications éffectuées avec succès');

            return $this->redirectToRoute('view_outing', [
                'id' =>$outing->getId(),
            ]);
        }

        return $this->render(
            'outing/manageOuting.html.twig',
            [
                'outingForm' => $updateForm->createView(),
                'cardTitle' => 'Modifier',
                'btnSuppr' => true,
                'id' => $id
            ]
        );



    }


    /**
     *
     * @Route("/subscribe/{id}", name="outing_subscribe",methods={"GET"})
     */
    public function subscribe($id, EntityManagerInterface $em, Request $request)
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED') && $request->isXmlHttpRequest()) {
            $user = $this->getUser();

            $outingRepo = $this->getDoctrine()->getRepository(Outing::class);
            $outing = $outingRepo->find($id);
            if (isset($outing) && $outing->canSubscribe()) {
                if (($outing->isParticipant($user))) {
                    return new Response('Vous êtes déjà inscrit', Response::HTTP_FORBIDDEN);
                }
                $outing->addParticipant($user);

                $em->persist($outing);
                $em->flush();


                return new JsonResponse(['user'=>$user->getParticipantData()]);
            } else {
                return new Response('Cette sortie n\'est plus disponible', Response::HTTP_NOT_FOUND);
            }
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     *
     * @Route("/unsubscribe/{id}", name="outing_unsubscribe",methods={"GET"})
     */
    public function unsubscribe($id, EntityManagerInterface $em, Request $request)
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED') && $request->isXmlHttpRequest()) {
            $user = $this->getUser();

            $outingRepo = $this->getDoctrine()->getRepository(Outing::class);
            $outing = $outingRepo->find($id);
            if (isset($outing)) {
                if (($outing->isParticipant($user))) {
                    $outing->removeParticipant($user);
                    $em->persist($outing);
                    $em->flush();
                    return new JsonResponse(['user'=>$user->getParticipantData()]);
                }
            } else {
                return new Response('Cette sortie n\'est plus disponible', Response::HTTP_NOT_FOUND);
            }
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * Donne les infos (rue,cp,latitude...) lors d'un renseignement d'un lieu
     * @Route("/createOuting/{id}", name="create_outing_id")
     */
    public function idLieu($id)
    {
        $locationRepo = $this->getDoctrine()->getRepository(Location::class);
        $location = $locationRepo->findLocationandCity($id);

              return new JsonResponse(['infosLieu'=>$location]);
    }


    /**
     * @Route("/detail/{id}",name="view_outing",requirements={"id"="\d+"})
     */
    public function detail($id)
    {

        $outing = $this->getOuting($id);
        if ($outing===false)
        {
            return$this->redirectToRoute("home");
        }elseif($outing->getState()->getId() != 5 ){
            return $this->render("outing/viewOuting.html.twig",[
                'outing'=>$outing
            ]);
        }
    }

    /**
     * @Route("/cancel/{id}",name="outing_cancel", requirements={"id"="\d+"})
     */
    public function cancel($id ,Request $request, EntityManagerInterface $em,StateRepository $stateRepository)
    {
        $outing = $this->getOuting($id);
        if ($outing === false) {
            return $this->redirectToRoute("home");
        } else {

            $motif = $request->get("motif");
            if (isset($motif))
            {
                if (strlen($motif)>5)
                {
                    //modification des infos
                    $infosOuting = $outing->getInfosOuting();
                    $auj= (new DateTime('now'))->format('d/m/Y');
                    $motif = "ANNULEE LE ".$auj." \nMotif: ".$motif."\n".$infosOuting;
                    $outing->setInfosOuting($motif);

                    // modifications de l'etat
                    $outing->setState($stateRepository->find('4'));


                    $em->flush();

                    $this->addFlash('success','Votre sortie est bien annulée');
                    return  $this->redirectToRoute('home');



                }else{
                    $this->addFlash('warning','Veuillez décrive le motif de l\'annulation en minimum 5 caractères');
                }
            }
            return $this->render('outing/cancel.html.twig', [
                'outing' => $outing
            ]);
        }
    }

    /**
     *
     * @Route("/publish/{id}", name="outing_publish", methods={"GET"}, requirements={"id": "\d+"})
     */
    public function publish($id,
                            EntityManagerInterface $em,
                            OutingRepository $outingRepository,
                            StateRepository $stateRepository,
                            Request $request)
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED') && $request->isXmlHttpRequest()) {

            $outing = $outingRepository->find($id);

            if (isset($outing)) {
                $outing->setState($stateRepository->find(2));
                $em->flush();
                return new Response('Cette sortie est maintenant publier', Response::HTTP_OK);
            } else {
                return new Response('Cette sortie n\'est plus disponible', Response::HTTP_NOT_FOUND);
            }
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * retourne outing demandé ou false
     * ajoute flash error
     */
    public function getOuting($id)
    {
        $outingRepo = $this->getDoctrine()->getRepository(Outing::class);
        $outing = $outingRepo->find($id);

        if (isset($outing)) {

            return $outing;
        } else {
            $this->addFlash('warning', "Cette sortie n'est plus disponible");
            return false;
        }
    }
}




