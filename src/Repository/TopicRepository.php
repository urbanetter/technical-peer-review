<?php

namespace App\Repository;

use App\Entity\Developer;
use App\Entity\Team;
use App\Entity\Topic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Topic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Topic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Topic[]    findAll()
 * @method Topic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topic::class);
    }

    public function teamAverages(Team $team)
    {
        return $this->createQueryBuilder('t')
            ->select('MAX(t.id) AS id, MAX(t.name) AS name, AVG(a.value) AS avg')
            ->where('t.team = :team')
            ->setParameter('team', $team)
            ->join('t.assessments', 'a')
            ->groupBy('a.topic')
            ->orderBy('t.id')
            ->getQuery()
            ->getResult();
    }

    public function external(Developer $developer)
    {
        return $this->createQueryBuilder('t')
            ->select('MAX(t.id) AS id, MAX(t.name) AS name, AVG(a.value) AS avg')
            ->where('a.target = :developer')
            ->andWhere('a.source <> :developer')
            ->setParameter('developer', $developer)
            ->join('t.assessments', 'a')
            ->groupBy('a.topic')
            ->orderBy('t.id')
            ->getQuery()
            ->getResult();
    }
}
