<?php

namespace App\Controller;


use App\Entity\Location;
use App\Entity\Outing;
use App\Form\OutingType;
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
    public function createOuting(EntityManagerInterface $em,Request $request)
    {

        $outing = new Outing();


        $outingForm = $this->createForm(OutingType::class,$outing);

        return $this->render('outing/createOuting.html.twig', [
            'outingForm' => $outingForm->createView()
        ]);
    }

    /**
     *
     * @Route("/subscribe/{id}", name="outing_subscribe",methods={"GET"})
     */
    public function subscribe($id,EntityManagerInterface $em,Request $request)
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
//                $user->addOutingsParticipate($outing);
                $em->persist($outing);
                $em->flush();
//                $em->persist($user);

                return new Response('OK', Response::HTTP_OK);
            } else {
                return new Response('Cette sortie n\'est plus disponible', Response::HTTP_NOT_FOUND);
            }
        }else{
            return $this->redirectToRoute('home');
        }
    }

    /**
     *
     * @Route("/unsubscribe/{id}", name="outing_unsubscribe",methods={"GET"})
     */
    public function unsubscribe($id,EntityManagerInterface $em,Request $request)
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
                    return new Response('OK', Response::HTTP_OK);
                }
            } else {
                return new Response('Cette sortie n\'est plus disponible', Response::HTTP_NOT_FOUND);
            }
        } else {
            return $this->redirectToRoute('home');
            $outingRepo = $this->getDoctrine()->getRepository(Outing::class);
            $outing = $outingRepo->find($id);
            if (isset($outing) && $outing->canSubscribe()) {
                if (($outing->isParticipant($user))) {
                    return new Response('Vous êtes déjà inscrit', Response::HTTP_FORBIDDEN);
                }
                $outing->addParticipant($user);
                $em->persist($outing);
                $em->flush();


                return new Response('OK', Response::HTTP_OK);
            } else {
                return new Response('Cette sortie n\'est plus disponible', Response::HTTP_NOT_FOUND);
            }
        }}

        /**
         * @Route("/createOuting/{id}", name="create_outing_id",methods={"GET"})
         */
        public function idLieu($id)
        {
            $city = $this->getDoctrine()->getRepository(Location::class);
            $citiesDatas = $city->find($id);
            dump($citiesDatas);
            //return new JsonResponse(['infosLieu'=>$citiesDatas]);
        }
    }


