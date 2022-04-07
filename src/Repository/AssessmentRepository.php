<?php

namespace App\Repository;

use App\Entity\Assessment;
use App\Entity\Developer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Assessment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Assessment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Assessment[]    findAll()
 * @method Assessment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssessmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assessment::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Assessment $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Assessment $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Assessment[] Returns an array of Assessment objects
     */
    public function selfAssessments(Developer $developer)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.source = :developer')
            ->andWhere('a.target = :developer')
            ->setParameter('developer', $developer)
            ->orderBy('a.id')
            ->getQuery()
            ->getResult()
        ;
    }

}
