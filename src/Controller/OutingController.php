<?php

namespace App\Controller;


use App\Entity\Location;
use App\Entity\Outing;
use App\Entity\State;
use App\Form\OutingType;
use App\Repository\OutingRepository;
use App\Repository\StateRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        dump($outing);
        if ($outingForm->isSubmitted() && $outingForm->isValid())

        {

            dump($outingForm);
            //Chargement de l'état
            $stateRepo = $this->getDoctrine()->getRepository(State::class);
            $outing->setState($stateRepo->find('1'));
            //Chargement de l'organizer
            $outing->setOrganizer($this->getUser());
            //Chargement du site
            $outing->setSite($this->getUser()->getSite());
            $em->persist($outing);
            $em->flush();

            /*
            return $this->redirectToRoute('view_outing',[
                "outing"=> $outing,
            ]);
            */
        }
        return $this->render(
            'outing/createOuting.html.twig',
            [
                'outingForm' => $outingForm->createView(),

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




