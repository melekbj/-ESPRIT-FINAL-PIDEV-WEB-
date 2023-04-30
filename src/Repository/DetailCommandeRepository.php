<?php

namespace App\Repository;

use App\Entity\DetailCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailCommande>
 *
 * @method DetailCommande|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailCommande|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailCommande[]    findAll()
 * @method DetailCommande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailCommande::class);
    }

    public function save(DetailCommande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DetailCommande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    /**
     * @return DetailCommande[] Returns an array of DetailCommande objects
     */
    public function findByCommande($value): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.commande = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

          /**
   * @return DetailCommande[] Returns an array of  Detail_Commande objects
     */
    public function findByStore($store,$etat,$order): array
    {    $qb = $this->createQueryBuilder('c')
        ->andWhere('c.store = :store')
        ->setParameter('store', $store)
        ->orderBy('c.etat',"DESC");

    if ($etat !== null && $etat !== '') {
        $qb->andWhere('c.etat = :etat')
            ->setParameter('etat', $etat);
    }

    if ($order !== null && $order !== '') {
        $qb->addOrderBy('c.prix_total', $order);
    }

    return $qb->getQuery()->getResult();
}


//    /**
//     * @return DetailCommande[] Returns an array of DetailCommande objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DetailCommande
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
