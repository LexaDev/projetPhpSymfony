<?php

namespace App\Controller;


use App\Entity\Outing;
use App\Form\OutingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
}
