<?php

namespace App\Repository;

use App\Entity\Confidence;
use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Confidence>
 *
 * @method Confidence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Confidence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Confidence[]    findAll()
 * @method Confidence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfidenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Confidence::class);
    }

    public function add(Confidence $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Confidence $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByTeam(Team $team)
    {
        return $this->createQueryBuilder('c')
            ->join('c.topic', 't')
            ->where('t.team = :team')
            ->setParameter('team', $team)
            ->getQuery()
            ->getResult();
    }
}
