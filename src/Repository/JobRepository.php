<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Job;
<<<<<<< HEAD
use Doctrine\ORM\AbstractQuery;
=======
use App\Entity\Category;
>>>>>>> 12d2753689782e3753e0748cf42f637e71f455fa
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\AbstractQuery;

class JobRepository extends EntityRepository
{
    /**
     * @param Category $category
     * @return AbstractQuery
     */
    public function getPaginatedActiveJobsByCategory(Category $category):AbstractQuery
    {
        return $this->createQueryBuilder('j')
                    ->where('j.category= :category')
                    ->andwhere('j.expiresAt> :date')
                    ->setParameter('category', $category)
                    ->setParameter('date', new \DateTime())
                    ->getQuery();
    }


    /**
     * @param int|null $categoryId
     *
     * @return Job[]
     */
    public function findActiveJobs(int $categoryId = null)
    {
        $qb = $this->createQueryBuilder('j')
            ->where('j.expiresAt > :date')
            ->setParameter('date', new \DateTime())
            ->orderBy('j.expiresAt', 'DESC');

        if ($categoryId) {
            $qb->andWhere('j.category = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $id
     *
     * @throws NonUniqueResultException
     *
     * @return Job|null
     */
    public function findActiveJob(int $id) : ?Job
    {
        return $this->createQueryBuilder('j')
            ->where('j.id = :id')
            ->andWhere('j.expiresAt > :date')
            ->setParameter('id', $id)
            ->setParameter('date', new \DateTime())
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Category $category
     *
     * @return AbstractQuery
     */
    public function getPaginatedActiveJobsByCategoryQuery(Category $category) : AbstractQuery
    {
        return $this->createQueryBuilder('j')
            ->where('j.category = :category')
            ->andWhere('j.expiresAt > :date')
            ->setParameter('category', $category)
            ->setParameter('date', new \DateTime())
            ->getQuery();
    }
}
