<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    // Find/search articles by title
    /**
     * @param string $query
     * @return int|mixed|string
     */
    public function findArticlesByName(string $query)
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->orX(
                        $qb->expr()->like('p.title', ':query'),
                        $qb->expr()->like('p.content', ':query'),
                    ),
                    $qb->expr()->isNotNull('p.created_at')
                )
            )
            ->setParameter('query', '%' . $query . '%')
        ;
        return $qb
            ->getQuery()
            ->getResult();
    }

    // 5 last articles
    /**
     * @return array
     */
    public function lastArticles(): array
    {
        return $this->createQueryBuilder('a')
            //->andWhere('a.exampleField = :val')
            //->setParameter('val', $value)
            //->andWhere('a.is_active = true')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(5)
            ->select('a.title', 'a.slug', 'a.created_at', 'a.image', 'a.views')
            ->getQuery()
            ->getResult()
        ;
    }

    // 3 most popular last articles (by views)
    /**
    * @return Article[] Returns an array of Articles objects
    */
    public function popularArticles(): array
    {
        return $this->createQueryBuilder('a')
            //->andWhere('a.exampleField = :val')
            //->setParameter('val', $value)
            //->andWhere('a.is_active = true')
            ->orderBy('a.views', 'DESC')
            ->setMaxResults(5)
            ->select('a.title', 'a.slug', 'a.created_at', 'a.image', 'a.views')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array
     */
    public function totalViews(): array
    {
        return $this->createQueryBuilder('v')
            ->select('SUM(v.views) AS totalViews')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array
     */
    public function findAllArtCatTagUne(): array
    {
        return $this->createQueryBuilder('find')
            ->leftJoin('find.article', 'fa')
            ->addSelect('fa')
            ->leftJoin('find.tag', 'ft')
            ->addSelect('ft')
            ->orderBy('fa.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
