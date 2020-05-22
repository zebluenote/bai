<?php

namespace App\Repository;

use Exception;
use App\Entity\Carousel;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Carousel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Carousel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Carousel[]    findAll()
 * @method Carousel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarouselRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Carousel::class);
    }

    /**
     * Retourne un carousel par son slug uniquement s'il est au status visible (status=true)
     * @return Carousel|null Returns a Carousel object
     */
    public function getIfVisible($slug)
    {
        try {
            $result = $this->createQueryBuilder('c')
            ->andWhere('c.slug = :val')
            ->andWhere('c.status = 1')
            ->setParameter('val', $slug)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult()
            ;
            return $result;
        } catch(Exception $e) {
            return null;            
        }
    }

    /*
    public function findOneBySomeField($value): ?Carousel
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
