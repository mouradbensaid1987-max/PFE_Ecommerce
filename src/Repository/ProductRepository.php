<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }
/*
    public function findActiveProducts(?string $search = null)
      {
          $qb = $this->createQueryBuilder('p')
              ->where('p.isActive = :active')
              ->setParameter('active', true)
              ->orderBy('p.createdAt', 'DESC');

          if (!empty($search)) {
              $qb->andWhere('p.name LIKE :search OR p.description LIKE :search')
                  ->setParameter('search', '%' . $search . '%');
          }

          return $qb;
      }
*/

    public function findFiltered(array $filters, bool $onlyActive = true)
      {
          $qb = $this->createQueryBuilder('p')
                     ->orderBy('p.createdAt', 'DESC');

          if ($onlyActive) 
            {
                $qb->andWhere('p.isActive = :active')
                   ->setParameter('active', true);
            }

          if (!empty($filters['search'])) 
            {
                $qb->andWhere('p.name LIKE :search OR p.description LIKE :search OR p.ref LIKE :search')
                   ->setParameter('search', '%'.$filters['search'].'%');
            }

          if (!empty($filters['category'])) 
            {
                $qb->andWhere('p.category = :category')
                   ->setParameter('category', $filters['category']);
            }

          if (!empty($filters['materiau'])) 
            {
                $qb->andWhere('p.materiau = :materiau')
                   ->setParameter('materiau', $filters['materiau']);
            }

          if (!empty($filters['diametreMm'])) 
            {
                $qb->andWhere('p.diametreMm = :diametreMm')
                ->setParameter('diametreMm', $filters['diametreMm']);
            }

          if (!empty($filters['typeTete'])) 
            {
                $qb->andWhere('p.typeTete = :typeTete')
                ->setParameter('typeTete', $filters['typeTete']);
            }

          if (!empty($filters['typeEmpreinte'])) 
            {
                $qb->andWhere('p.typeEmpreinte = :typeEmpreinte')
                ->setParameter('typeEmpreinte', $filters['typeEmpreinte']);
            }

          if (!empty($filters['unite'])) 
            {
                $qb->andWhere('p.unite = :unite')
                ->setParameter('unite', $filters['unite']);
            }

          if (!empty($filters['longueurMin'])) 
            {
                $qb->andWhere('p.longueurMm >= :longueurMin')
                ->setParameter('longueurMin', (int) $filters['longueurMin']);
            }

          if (!empty($filters['longueurMax'])) 
            {
                $qb->andWhere('p.longueurMm <= :longueurMax')
                ->setParameter('longueurMax', (int) $filters['longueurMax']);
            }

          if (isset($filters['isActive']) && $filters['isActive'] !== '' && $filters['isActive'] !== null) 
            {
                $qb->andWhere('p.isActive = :isActive')
                ->setParameter('isActive', (bool) $filters['isActive']);
            }

        return $qb;
      }

    public function findDistinctValues(string $field): array
      {
          $allowed = ['materiau', 'diametreMm', 'typeTete', 'typeEmpreinte', 'unite'];

          if (!in_array($field, $allowed, true)) 
            {
                throw new \InvalidArgumentException('Champ non autorisé pour findDistinctValues().');
            }

          return $this->createQueryBuilder('p')
                      ->select('DISTINCT p.'.$field.' AS value')
                      ->where('p.'.$field.' IS NOT NULL')
                      ->andWhere("p.".$field." != ''")
                      ->orderBy('p.'.$field, 'ASC')
                      ->getQuery()
                      ->getSingleColumnResult();
      }











//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
