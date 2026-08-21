<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/** Packages, package groups and configurable package options. */
final class Packages extends Resource
{
    /**
     * POST /package/create.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): array
    {
        return $this->client->post('/package/create', $data);
    }

    /**
     * POST /package/update.
     *
     * @param array<string, mixed> $data
     */
    public function update(array $data): array
    {
        return $this->client->post('/package/update', $data);
    }

    /** POST /package/delete */
    public function delete(int $id): array
    {
        return $this->client->post('/package/delete', ['id' => $id]);
    }

    /** GET /package/get */
    public function get(int $id): array
    {
        return $this->client->get('/package/get', ['id' => $id]);
    }

    /** GET /package/show */
    public function show(int $id): array
    {
        return $this->client->get('/package/show', ['id' => $id]);
    }

    /** GET /package/lists. Filters: featured, per_page. */
    public function list(array $filters = []): array
    {
        return $this->client->get('/package/lists', $filters);
    }

    /** GET /package/get-by-customer */
    public function listByCustomer(int $customerId, ?int $perPage = null): array
    {
        $query = ['customer_id' => $customerId];
        if ($perPage !== null) {
            $query['per_page'] = $perPage;
        }

        return $this->client->get('/package/get-by-customer', $query);
    }
}
