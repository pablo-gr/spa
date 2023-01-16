<?php

namespace App\Repository;

use App\Entity\Scheduler;
use App\Entity\Service;
use DateTime;

interface SchedulerRepositoryInterface
{
    /**
     * @param Service $service
     * @param string $date
     * @return Scheduler[]
     */
    public function findByServiceAndDateRange(Service $service, string $date): array;

    /**
     * @param Service $service
     * @param DateTime $date
     * @return Scheduler|null
     */
    public function findOneByServiceAndDate(Service $service, DateTime $date): ?Scheduler;
}