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

    public function findCityAround(float $latitude, float $longitude, int $distance)
    {
        // Approximation : 1° de latitude ≈ 111 km
        $deltaLat = $distance / 111;
        
        // La distance en longitude dépend de la latitude (ajustée avec cosinus)
        $deltaLon = $distance / (111 * cos(deg2rad($latitude)));

        $qb = $this->createQueryBuilder('r')
            // Filtrage initial avec une "bounding box" (rectangle grossier autour du point)
            ->where('r.latitude BETWEEN :minLat AND :maxLat')
            ->andWhere('r.longitude BETWEEN :minLon AND :maxLon')
            ->setParameter('minLat', $latitude - $deltaLat)
            ->setParameter('maxLat', $latitude + $deltaLat)
            ->setParameter('minLon', $longitude - $deltaLon)
            ->setParameter('maxLon', $longitude + $deltaLon)

            // Calcul précis de la distance avec la formule de la loi des cosinus sphérique
            ->addSelect(
                '(6371 * ACOS(
                    COS(RADIANS(:latitude)) 
                    * COS(RADIANS(r.latitude)) 
                    * COS(RADIANS(r.longitude) - RADIANS(:longitude)) 
                    + SIN(RADIANS(:latitude)) 
                    * SIN(RADIANS(r.latitude))
                )) AS distance'
            )

            // Filtrer uniquement les villes dans le rayon spécifié
            // Having car WHERE ne peut pas filtrer sur des alias 
            ->having('distance <= :distance')
            ->setParameter('latitude', $latitude)
            ->setParameter('longitude', $longitude)
            ->setParameter('distance', $distance)

            // Trier les résultats du plus proche au plus éloigné
            ->orderBy('distance', 'ASC');

        return $qb->getQuery();
    }

    public function findAllRdv()
    {
        return $this->createQueryBuilder('r')
            ->getQuery();
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
