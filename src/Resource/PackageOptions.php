<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/** Configurable package options (custom fields). */
final class PackageOptions extends Resource
{
    /**
     * POST /package/option/create. Data: internal_name, display_name, field_type, options[], required.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): array
    {
        return $this->client->post('/package/option/create', $data);
    }

    /**
     * POST /package/option/update.
     *
     * @param array<string, mixed> $data
     */
    public function update(array $data): array
    {
        return $this->client->post('/package/option/update', $data);
    }

    /** POST /package/option/delete */
    public function delete(int $id): array
    {
        return $this->client->post('/package/option/delete', ['id' => $id]);
    }

    /** GET /package/option/get */
    public function get(int $id): array
    {
        return $this->client->get('/package/option/get', ['id' => $id]);
    }

    /** GET /package/option/lists */
    public function list(?int $perPage = null): array
    {
        return $this->client->get('/package/option/lists', array_filter(['per_page' => $perPage]));
    }
}
