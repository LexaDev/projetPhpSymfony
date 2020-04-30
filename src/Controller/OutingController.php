<?php

namespace App\Controller;


use App\Entity\Outing;
use App\Form\OutingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function subscribe($id,EntityManagerInterface $em, Request $request)
    {
        $user = $this->getUser();
        //$id = $request->get('id');
        dump($id);
        $outingRepo = $this->getDoctrine()->getRepository(Outing::class);
        $outing = $outingRepo->find($id);
        if (isset($outing))
        {
            if (($outing->isParticipant($user)))
            {
                return new Response('User deja participant',Response::HTTP_FORBIDDEN);
            }
            $outing->addParticipant($user);
            $em->persist($outing);
            $em->flush();


            return new Response('OK',Response::HTTP_OK);
        }else{
            return new Response('outing not find',Response::HTTP_NOT_FOUND);
        }

    }
}
