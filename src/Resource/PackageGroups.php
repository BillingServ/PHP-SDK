<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/** Package groups. */
final class PackageGroups extends Resource
{
    /**
     * POST /package/group/create.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): array
    {
        return $this->client->post('/package/group/create', $data);
    }

    /**
     * POST /package/group/update.
     *
     * @param array<string, mixed> $data
     */
    public function update(array $data): array
    {
        return $this->client->post('/package/group/update', $data);
    }

    /** POST /package/group/delete */
    public function delete(int $id): array
    {
        return $this->client->post('/package/group/delete', ['id' => $id]);
    }

    /** GET /package/group/get */
    public function get(int $id): array
    {
        return $this->client->get('/package/group/get', ['id' => $id]);
    }

    /** GET /package/group/lists */
    public function list(?int $perPage = null): array
    {
        return $this->client->get('/package/group/lists', array_filter(['per_page' => $perPage]));
    }
}
