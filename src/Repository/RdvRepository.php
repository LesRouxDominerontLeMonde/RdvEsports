<?php

namespace App\Repository;

use App\Entity\Rdv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rdv>
 */
class RdvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rdv::class);
    }
    
    
    /**
     * @return \Doctrine\ORM\Query
     */
    // retourne une Query qui est passe au paginator
    public function findByPlaceQuery(?string $place): \Doctrine\ORM\Query
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.category', 'c')
            ->addSelect('c');

        if ($place) {
            $qb->where('r.place = :place')
               ->setParameter('place', $place);
        }

        return $qb->getQuery();
    }

    /**
     * @param int $userId
     * @return Rdv[]
     */

    public function searchAllFromUser($userId): array
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.category', 'c')
            ->select('r', 'c')
            ->Where('r.author = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Rdv[] Returns an array of Rdv objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Rdv
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
