<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Recipe>
 *
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function paginateRecipe(int $page, int $limit): PaginationInterface
    {
        return $this -> paginator->paginate(
            $this -> createQueryBuilder('r')->leftJoin('r.category', 'c')->select('r','c'),
            $page,
            3,
            [
                'distinct' => false,
                'sortFieldAllowList' => ['r.id']
            ]
        );
        // return new Paginator($this 
        //     ->createQueryBuilder('r')
        //     ->setFirstResult(($page - 1) * $limit)
        //     ->setMaxResults($limit)
        //     ->getQuery()
        //     ->setHint(Paginator::HINT_ENABLE_DISTINCT, false),false
        // );
    }

    public function findTotalDuration() :int
    {
        return $this -> createQueryBuilder('r')
            ->select('SUM(r.duration) as total')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Recipe[]
     */
    public function findWithDurationLowerThan(int $duration): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.duration <= :duration')
            ->orderBy('r.duration', 'ASC')
            ->setMaxResults(100)
            ->setParameter('duration', $duration)
            ->getQuery()
            ->getResult();
    }

    //t'aurais pu utiliser du sql natif mais, il aurait fallu donner des noms de propriétés différents
    //ici la prop slug est la même pour recipe et category, donc le slug de category écrase celui de recipe...
    //ou alors mettre des alias dans la requête mais flemme
    public function findAll2(EntityManagerInterface $em): array
    {
        $sql = "SELECT * FROM recipe LEFT JOIN category ON recipe.category_id = category.id";

        $stmt = $em->getConnection()->prepare($sql);
        $recipes = $stmt->executeQuery()->fetchAllAssociative();
        return $recipes;
    }

    //    /**
    //     * @return Recipe[] Returns an array of Recipe objects
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

    //    public function findOneBySomeField($value): ?Recipe
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
