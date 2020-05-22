<?php

namespace App\Repository;

use App\Entity\Faq;
use App\Entity\FaqCategory;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Faq|null find($id, $lockMode = null, $lockVersion = null)
 * @method Faq|null findOneBy(array $criteria, array $orderBy = null)
 * @method Faq[]    findAll()
 * @method Faq[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FaqRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Faq::class);
    }

    public function findAll()
    {
        return $this->findBy(array(), array('categoryId' => 'ASC', 'num' => 'ASC'));
    }

    public function findOrderByNum(FaqCategory $category)
    {
        return $this->findByCategory($category, array('num' => 'DESC'));
    }

    // /**
    //  * @return Faq[] Returns an array of Faq objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Faq
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
