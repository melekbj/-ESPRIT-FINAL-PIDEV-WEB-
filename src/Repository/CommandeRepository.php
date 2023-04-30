<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 *
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function save(Commande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Commande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

       /**
   * @return Commande[] Returns an array of Commande objects
     */
    public function findByClient($value): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

     /**
   * @return Commande[] Returns an array of Commande objects
     */
            // the min max must be inserted correctly  in the call function 
            // ----  u can insert null for $order  ----
    public function findByClientByPrice($value,$min,$max,$order): array
    {  if ($order==null)
        {
            $order='ASC';
        }
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->andWhere('c.prix <= :max')
            ->andWhere('c.prix >= :min')
            ->setParameter('user', $value)
            ->setParameter('max', $max)
            ->setParameter('min', $min)
            ->orderBy('c.prix', $order)
            ->getQuery()
            ->getResult()
        ;
    }

      /**
   * @return Commande[] Returns an array of Commande objects
     */
    public function findByClientByEtat($value,$etat): array
    {  
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->andWhere('c.etat = :etat')
            ->setParameter('user', $value)
            ->setParameter('etat', $etat)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

       /**
   * @return Commande[] Returns an array of Commande objects
     */
    public function findByClientByEtatByPrice($value,$etat,$min,$max,$order): array
    {   $order= $order == null? 'ASC':$order;
        if ($max == null)
        {
            $max='';
        }
        else{
            $max =$max;
        }
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->andWhere('c.etat = :etat')
            ->andWhere('c.prix <= :max')
            ->andWhere('c.prix >= :min')
            ->setParameter('user', $value)
            ->setParameter('etat', $etat)
            ->setParameter('max', $max)
            ->setParameter('min', $min)
            ->orderBy('c.id', $order)
            ->getQuery()
            ->getResult()
        ;
    }
         /**
   * @return Commande[] Returns an array of Commande objects
     */
    public function findByClientByEtatByPrice2($value,$etat,$min,$max,$order): array
    {    $qb = $this->createQueryBuilder('c')
        ->andWhere('c.user = :user')
        ->setParameter('user', $value);

    if ($min !== null && $min !== '') {
        $qb->andWhere('c.prix >= :min')
            ->setParameter('min', $min);
    }

    if ($max !== null && $max !== '') {
        $qb->andWhere('c.prix <= :max')
            ->setParameter('max', $max);
    }

    if ($etat !== null && $etat !== '') {
        $qb->andWhere('c.etat = :etat')
            ->setParameter('etat', $etat);
    }

    if ($order !== null && $order !== '') {
        $qb->orderBy('c.id', $order);
    }

    return $qb->getQuery()->getResult();
}

//    /**
//     * @return Commande[] Returns an array of Commande objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Commande
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
