<?php

namespace App\Service;

use App\Entity\Booking;
use App\Repository\BookingRepositoryInterface;
use App\Repository\SchedulerRepositoryInterface;
use App\Repository\ServiceRepositoryInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class BookingService
{
    protected ServiceRepositoryInterface $serviceRepository;
    protected SchedulerRepositoryInterface $schedulerRepository;
    protected BookingRepositoryInterface $bookingRepository;
    protected EntityManagerInterface $em;

    public function __construct(
        ServiceRepositoryInterface $serviceRepository,
        SchedulerRepositoryInterface $schedulerRepository,
        BookingRepositoryInterface $bookingRepository,
        EntityManagerInterface $em,
    )
    {
        $this->serviceRepository = $serviceRepository;
        $this->schedulerRepository = $schedulerRepository;
        $this->bookingRepository = $bookingRepository;
        $this->em = $em;
    }

    /**
     * @param int $id
     * @param string $date
     * @param string $clientName
     * @param string $clientEmail
     * @return array
     * @throws Exception
     */
    public function create(int $id, string $date, string $clientName, string $clientEmail): array {
        $service = $this->serviceRepository->findById($id);
        if (!$service) {
            throw new Exception('Service not found', Response::HTTP_NOT_FOUND);
        }

        $bookingDate = new DateTime($date);
        $scheduler = $this->schedulerRepository->findOneByServiceAndDate($service, $bookingDate);
        if (!$scheduler) {
            throw new Exception('The service is not scheduled for this date', Response::HTTP_NOT_FOUND);
        }

        $startAt = $scheduler->getStartAt();
        $endAt = null;
        while ($startAt < $scheduler->getEndAt() && $endAt === null) {
            $passHour = (clone $startAt)->modify('+1 hours');
            if ($bookingDate >= $startAt && $bookingDate <= $passHour) {
                $endAt = $passHour;
            } else {
                $startAt = (clone($startAt)->modify('+1 hours'));
            }
        }

        $existentBooking = $this->bookingRepository->findOneByServiceAndDateRange($service, $startAt, $endAt);
        if ($existentBooking) {
            throw new Exception('Booking date already exists for this service', Response::HTTP_BAD_REQUEST);
        }

        $booking = new Booking();
        $booking->setService($service);
        $booking->setPrice($service->getPrice());
        $booking->setClientEmail($clientEmail);
        $booking->setClientName($clientName);
        $booking->setDate($bookingDate);
        $this->em->persist($booking);
        $this->em->flush();

        return [
            'id' => $booking->getId(),
            'service' => $booking->getService()->getId(),
            'client-name' => $booking->getClientName(),
            'client-email' => $booking->getClientEmail(),
            'date' => $booking->getDate(),
            'price' => $booking->getPrice(),
        ];
    }
}