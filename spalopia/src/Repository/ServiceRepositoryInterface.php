<?php

namespace App\Repository;

use App\Entity\Service;

interface ServiceRepositoryInterface
{
    /**
     * @param array $orders
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findPaginated(array $orders, int $limit, int $offset): array;

    /**
     * @param int $id
     * @return Service|null
     */
    public function findById(int $id): ?Service;
}