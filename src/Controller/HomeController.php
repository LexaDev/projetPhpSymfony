<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Entity\Site;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(EntityManagerInterface $em,
                         Request $request,
                         PaginatorInterface $paginator)
    {
        $nameModele = $request->get('sortieSearch');
        $site = $request->get('site');
        $dateStart = $request->get('dateStart');
        $dateEnd = $request->get('dateEnd');
        $organizer = $request->get('organizer') ? $this->getUser() : null;
        $registered = $request->get('registered') ? $this->getUser() : null;
        $unregistered = $request->get('unregistered') ? $this->getUser() : null;
        $finish = $request->get('finish') ? 'over' : null;

        $repo = $em->getRepository(Outing::class);
        $outings = $repo->findOutingsByCriterias($nameModele, $site, $dateStart, $dateEnd, $organizer, $registered, $unregistered, $finish);

        $siteRepo = $em->getRepository(Site::class);
        $sites = $siteRepo->findAll();

        $pagination = $paginator->paginate(
            $outings,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('default/home.html.twig', [
            'outings'       => $outings,
            'pagination'    => $pagination,
            'sites'         => $sites,
            'filters'       => [
                'sortieSearch'      => $nameModele,
                'site'              => $site,
                'dateStart'         => $dateStart,
                'dateEnd'           => $dateEnd,
                'organizer'         => $organizer,
                'registered'          => $registered,
                'unregistered'      => $unregistered,
                'finish'            => $finish,
            ]
        ]);
    }
}
