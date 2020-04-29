<?php

namespace App\Repository;

use App\Entity\Outing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Outing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Outing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Outing[]    findAll()
 * @method Outing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Outing::class);
    }

    public function findOutingsByCriterias($nameModele = null,
                                           $site = null,
                                           $dateStart = null,
                                           $dateEnd = null,
                                           $organizer = null,
                                           $registered = null,
                                           $unregistered = null,
                                           $finish = null)
    {
        $query = $this->createQueryBuilder('o')
            ->leftJoin('o.participants', 'p')
            ->innerJoin('o.state', 's')
            ->innerJoin('o.organizer', 'organizer')
            ->select('o', 'p', 's', 'organizer')
        ;

        if ($nameModele){
            $query->where('o.name LIKE :nameModele');
            $query->setParameter('nameModele', '%'.$nameModele.'%');
        }

        if ($site){
            $query->andWhere('o.site = :site');
            $query->setParameter('site', $site);
        }

        if ($dateStart AND $dateEnd){
            $query->andWhere($query->expr()->between('o.dateTimeStart', ':dateStart', ':dateEnd'));
            $query->setParameter('dateStart', $dateStart);
            $query->setParameter('dateEnd', $dateEnd);
        }

        if ($organizer){
            $query->andWhere('organizer = :organizer');
            $query->setParameter('organizer', $organizer);
        }

        if ($registered){
            $query->andWhere('p = :participant');
            $query->setParameter('participant', $registered);
        }

        if ($unregistered){
            $query->andWhere('p != :participant');
            $query->setParameter('participant', $unregistered);
        }

        if ($finish){
            $query->andWhere('o.state = :etat');
            $query->setParameter('etat', $finish);
        }

        $query->orderBy('o.dateTimeStart');

        return $query->getQuery()->getResult();
    }
}
