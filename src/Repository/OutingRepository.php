<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Outing;
use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Outing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Outing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Outing[]    findAll()
 * @method Outing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Outing::class);
        $this->paginator = $paginator;
    }

    /**
     * @param SearchData $search
     * @param Participant $user
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function findOutingsByCriterias(SearchData $search, Participant $user)
    {
        $conditionPosed = false;
        $query = $this->createQueryBuilder('o')
            ->leftJoin('o.participants', 'p')
            ->innerJoin('o.state', 's')
            ->innerJoin('o.organizer', 'organizer')
            ->select('o', 's', 'organizer', 'p')
        ;

        $baseCondition = $query->expr()->andX();
        $optionalCondition = $query->expr()->orX();

        if ($search->pattern){
            $conditionPosed = true;
            $baseCondition->add($query->expr()->like('o.name',':nameModele'));
            $query->setParameter('nameModele', '%'.$search->pattern.'%');
        }

        if ($search->site){
            $conditionPosed = true;
            $baseCondition->add($query->expr()->eq('o.site', ':site'));
            $query->setParameter('site', $search->site);
        }

        if ($search->dateStart AND $search->dateEnd){
            $conditionPosed = true;
            $baseCondition->add($query->expr()->between('o.dateTimeStart', ':dateStart', ':dateEnd'));
            $query->setParameter('dateStart', $search->dateStart);
            $query->setParameter('dateEnd', $search->dateEnd);
        }

        if ($search->organizer){
            $conditionPosed = true;
            $optionalCondition->add($query->expr()->eq('organizer', ':organizer'));
            $query->setParameter('organizer', $user);
        }

        if ($search->registered){
            $conditionPosed = true;
            $optionalCondition->add(':participant MEMBER OF o.participants');
            $query->setParameter('participant', $user);
        }

        if ($search->unregistered){
            $conditionPosed = true;
            $optionalCondition->add($query->expr()->andX(
                ':participant NOT MEMBER OF o.participants',
                $query->expr()->neq('organizer', ':organizer')
            ));
            $query->setParameter('participant', $user);
            $query->setParameter('organizer', $user);
        }

        if ($search->finished){
            $conditionPosed = true;
            $optionalCondition->add($query->expr()->eq('o.state', ':etat'));
            $query->setParameter('etat', 'over');
        }

        if ($conditionPosed){
            $query->where($query->expr()->andX($baseCondition, $optionalCondition));
        }

        $query->andWhere('o.dateTimeStart >= :OneMonthAgo')
            ->setParameter('OneMonthAgo', date_sub(new \DateTime(), new \DateInterval('P1M')));

        $query->orderBy('o.dateTimeStart');

        return $this->paginator->paginate(
            $query->getQuery(),
            $search->page,
            10
        );
    }
}
