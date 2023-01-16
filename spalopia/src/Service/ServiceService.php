<?php

namespace App\Service;

use App\Repository\BookingRepositoryInterface;
use App\Repository\SchedulerRepositoryInterface;
use App\Repository\ServiceRepositoryInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ServiceService
{
    protected ServiceRepositoryInterface $serviceRepository;
    protected SchedulerRepositoryInterface $schedulerRepository;
    protected BookingRepositoryInterface $bookingRepository;
    protected TranslatorInterface $translator;

    /**
     * @param ServiceRepositoryInterface $serviceRepository
     * @param SchedulerRepositoryInterface $schedulerRepository
     * @param BookingRepositoryInterface $bookingRepository
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ServiceRepositoryInterface $serviceRepository,
        SchedulerRepositoryInterface $schedulerRepository,
        BookingRepositoryInterface $bookingRepository,
        TranslatorInterface $translator
    ) {
        $this->serviceRepository = $serviceRepository;
        $this->schedulerRepository = $schedulerRepository;
        $this->bookingRepository = $bookingRepository;
        $this->translator = $translator;
    }

    /**
     * @param array $orders
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function list(array $orders, int $limit, int $offset): array
    {
        $entities = $this->serviceRepository->findPaginated($orders, $limit, $offset);
        $result = [];

        foreach ($entities as $entity) {
            $result[] = [
                'id' => $entity->getId(),
                'name' => $this->translator->trans($entity->getName()),
                'price' => $entity->getPrice(),
            ];
        }

        return $result;
    }

    /**
     * @param int $id
     * @param string $date
     * @return array
     * @throws Exception
     */
    public function getAvailableHours(int $id, string $date): array {
        $service = $this->serviceRepository->findById($id);
        if (!$service) {
            throw new Exception('Service not found', Response::HTTP_NOT_FOUND);
        }

        $schedulers = $this->schedulerRepository->findByServiceAndDateRange($service, $date);
        if (count($schedulers) === 0) {
            throw new Exception('The service is not scheduled for this date', Response::HTTP_NOT_FOUND);
        }

        $hours = [];
        foreach ($schedulers as $scheduler) {
            $startAt = $scheduler->getStartAt();
            while ($startAt < $scheduler->getEndAt()) {
                $endAt = (clone $startAt)->modify('1 hours');

                if (!$this->bookingRepository->findOneByServiceAndDateRange($service, $startAt, $endAt)) {
                    $hours[] = ['start_at' => $startAt, 'end_at' => $endAt];
                }

                $startAt = (clone $startAt)->modify('+1 hours');
            }
        }

        return $hours;
    }
}