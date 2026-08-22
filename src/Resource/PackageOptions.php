<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/**
 * Configurable package options.
 *
 * Each option carries a control `type` (0 select, 1 text, 2 number, 3 radio,
 * 4 checkbox, 5 switch) and predefined controls expose their choices in
 * `values`: [{id, option_id, display_name, cycle_type, price, fee}, ...].
 * Text and number controls return an empty `values` array.
 */
final class PackageOptions extends Resource
{
    /**
     * GET /package/option/lists
     *
     * @return array{success: bool, package_options?: array{data?: array<int, array<string, mixed>>}}
     */
    public function list(?int $perPage = null): array
    {
        return $this->client->get('/package/option/lists', array_filter(['per_page' => $perPage]));
    }

    /**
     * GET /package/option/get
     *
     * @return array{success: bool, package_option?: array<string, mixed>}
     */
    public function get(int $id): array
    {
        return $this->client->get('/package/option/get', ['id' => $id]);
    }

    /**
     * POST /package/option/create — data: internal_name, display_name,
     * field_type, options (choice strings), required ("Y"/"N").
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
}
