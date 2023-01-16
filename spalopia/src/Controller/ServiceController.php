<?php

namespace App\Controller;

use App\Service\ServiceService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    protected ServiceService $service;

    public function __construct(ServiceService $service)
    {
        $this->service = $service;
    }

    /**
     * List the available services for the spa.
     *
     * @Parameters:
     * - limit: set the limit of items list [default is 100]
     * - page: the page to list starting by 1 [default is 1]
     * - orders: the key indexed array to order by field.
     *
     * @Example: /api/service?orders[price]=ASC&page=2&limit=50
     *
     * @Response:
     *  A json contained the service models with the id, translated name and price. Example:
     *  [{id: 1, name: 'Body Mud Mask', price: 1.99}]
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/service', name: 'app_service', methods: [Request::METHOD_GET])]
    public function list(Request $request): JsonResponse
    {
        list($orders, $limit, $offset) = $this->getPagination($request);

        return $this->json(
            $this->service->list($orders, $limit, $offset)
        );
    }

    /**
     * List the available hours for a service in a specified date.
     * the already booking date with previous reservations are excluded!
     *
     * @Parameters:
     * - id: the service id
     * - date: the specified date to query
     *
     * @Example: /api/service/1/available-hours/2023-01-10
     *
     * @Response:
     *  A json contained the pair hours available for the service in the specified date. Example:
     *  [{start_at: '2023-01-10 09:00, end_at: '2023-01-10 10:00}, {start_at: '2023-01-10 11:00, end_at: '2023-01-10 12:00}]
     *
     * @param int $id
     * @param string $date
     * @return JsonResponse
     */
    #[Route('/api/service/{id}/available-hours/{date}', name: 'app_service_available_hours', methods: [Request::METHOD_GET])]
    public function availableHours(int $id, string $date): JsonResponse
    {
        try {
            return $this->json(
                $this->service->getAvailableHours($id, $date)
            );
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Return the orders, offset and limit params for request
     * to use in pagination entities list
     *
     * @param Request $request
     * @return array
     */
    protected function getPagination(Request $request): array {
        $orders = $request->get('orders', []);

        $limit = $request->get('limit', 100);
        if ($limit < 1) {
            $limit = 1;
        }

        $page = $request->get('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        return [$orders, $limit, ($page - 1) * $limit];
    }
}
