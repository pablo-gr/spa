<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Service;
use DateTime;

interface BookingRepositoryInterface
{
    public function findOneByServiceAndDateRange(Service $service, DateTime $startAt, DateTime $endAt): ?Booking;
}