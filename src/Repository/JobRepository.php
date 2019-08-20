<?php

namespace App\Repository;

use App\Entity\Job;
use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\AbstractQuery;

class JobRepository extends EntityRepository
{
    /**
     * @param Category $category
     * 
     * @return AbstractQuery
     */
    public function getPaginatedActiveJobsByCategory(Category $category):AbstractQuery
    {
        return $this->createQueryBuilder('j')
                ->where('j.category= :category')
                ->andWhere('j.expiresAt> :date')
                ->setParameter('category',$category)
                ->setParameter('date',new \DateTime())
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
}
