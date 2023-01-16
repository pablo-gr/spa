<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Service\BookingService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    protected BookingService $service;

    public function __construct(BookingService $service)
    {
        $this->service = $service;
    }

    /**
     * Create a booking for the specified service and date.
     * the
     *
     * @POST Parameters:
     * - id: the service id
     * - date: the specified date for reservation
     * - client-name: the client fullName
     * - client-email: the client email
     *
     * @Example: /api/booking {id: 1, date: '2023-01-10 09:30', client-name: 'John Doe', client-email: 'john.doe@testemail.com'}
     *
     * @Response:
     *  A json contained the booking data. Example:
     *  [{id: 11, service: 1, client-name: 'John Doe', client-email: 'john.doe@testemail.com', date: '2023-01-10 09:30', price: 1.99}]
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/booking', name: 'app_booking_create', methods: [Request::METHOD_POST])]
    public function create(Request $request): JsonResponse
    {
        try {
            $id = $request->get('id');
            $date = $request->get('date');
            $clientName = $request->get('client-name');
            $clientEmail = $request->get('client-email');

            if (!$id || !$date || !$clientEmail || !$clientName) {
                throw new Exception('The service id, booking date client name and client email are required', Response::HTTP_BAD_REQUEST);
            }

            return $this->json(
                $this->service->create($id, $date, $clientName, $clientEmail)
            );
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}