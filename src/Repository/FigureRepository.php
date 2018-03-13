<?php

namespace App\Repository;

use App\Entity\Figure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Figure|null find($id, $lockMode = null, $lockVersion = null)
 * @method Figure|null findOneBy(array $criteria, array $orderBy = null)
 * @method Figure[]    findAll()
 * @method Figure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FigureRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Figure::class);
    }

    //Affiche les dernères figures ajoutées
    public function findLatest(int $page = 1): Pagerfanta
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT f, a, c, s, i, v 
                FROM App:Figure f
                JOIN f.author a
                LEFT JOIN f.category c
                LEFT JOIN f.image i
                LEFT JOIN f.style s
                LEFT JOIN f.videos v
                WHERE f.publishedAt <= :now
                ORDER BY f.publishedAt DESC
            ')
            ->setParameter('now', new \DateTime())
        ;

        return $this->createPaginator($query, $page);
    }

    private function createPaginator(Query $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(Figure::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;

    }

    public function findBySearchQuery(string $rawQuery, int $limit = Figure::NUM_ITEMS): array
    {
        $query = $this->sanitizeSearchQuery($rawQuery);
        $searchTerms = $this->extractSearchTerms($query);

        if (0 === $this->count($searchTerms)){
            return [];
        }

        $queryBuilder = $this->createQueryBuilder('f');
        foreach ($searchTerms as $key => $term) {
            $queryBuilder
                ->orWhere('f.title LIKE :t '.$key)
                ->setParameter('f_'.$key, '%'.$term.'%');
        }

        return $queryBuilder
            ->orderBy('f.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    // Remove all non alphanumeric characters except whitespaces.
    private function sanitizeSearchQuery(string $query): string
    {
        return trim(preg_replace('/[[:space:]]+/', ' ', $query));
    }

    //Splits the search query into terms and removes irrelevant ones
    private function extractSearchTerms(string $searchQuery): array
    {
        $terms = array_unique(explode(' ', $searchQuery));
        return array_filter($terms, function ($term){
            return 2 <= mb_strlen($term);
        });
    }


}
