<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace App\Repository;

use App\Entity\Scheduler;
use App\Entity\Service;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<Scheduler>
 *
 * @method Scheduler|null find($id, $lockMode = null, $lockVersion = null)
 * @method Scheduler|null findOneBy(array $criteria, array $orderBy = null)
 * @method Scheduler[]    findAll()
 * @method Scheduler[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchedulerRepository extends ServiceEntityRepository implements SchedulerRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Scheduler::class);
    }

    public function save(Scheduler $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Scheduler $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Service $service
     * @param string $date
     * @return Scheduler[]
     * @throws Exception
     */
    public function findByServiceAndDateRange(Service $service, string $date): array
    {
        $startAt = (new DateTime($date))->setTime(0, 0);
        $endAt = (clone $startAt)->setTime(23, 59, 59);

        return $this->createQueryBuilder('s')
            ->where('s.service = :service')
            ->andWhere('s.start_at >= :startAt')
            ->andWhere('s.end_at <= :endAt')
            ->setParameters([
                'service' => $service->getId(),
                'startAt' => $startAt,
                'endAt' => $endAt,
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Service $service
     * @param DateTime $date
     * @return Scheduler|null
     * @throws NonUniqueResultException
     */
    public function findOneByServiceAndDate(Service $service, DateTime $date): ?Scheduler
    {
        return $this->createQueryBuilder('s')
            ->where('s.service = :service')
            ->andWhere('s.start_at <= :bookingDate')
            ->andWhere('s.end_at >= :bookingDate')
            ->setParameters([
                'service' => $service->getId(),
                'bookingDate' => $date,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
