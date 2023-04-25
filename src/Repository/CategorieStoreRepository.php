<?php

namespace App\Repository;

use App\Entity\CategorieStore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategorieStore>
 *
 * @method CategorieStore|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorieStore|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorieStore[]    findAll()
 * @method CategorieStore[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieStoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieStore::class);
    }

    public function save(CategorieStore $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CategorieStore $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

 
}
