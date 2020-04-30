<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Outing;
use App\Form\SearchForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(EntityManagerInterface $em,
                         Request $request)
    {
        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $searchForm = $this->createForm(SearchForm::class, $data);
        $searchForm->handleRequest($request);

        $outingRepo = $em->getRepository(Outing::class);
        $outings = $outingRepo->findOutingsByCriterias($data, $this->getUser());
        $strNbOutings = count($outings) > 1 ? ' sorties trouvées !': ' sortie trouvée !';

        return $this->render('default/home.html.twig', [
            'outings'       => $outings,
            'strNbOutings'  => $strNbOutings,
            'searchForm'    => $searchForm->createView(),
        ]);
    }
}
