<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/** Countries. */
final class Countries extends Resource
{
    /** GET /country/get */
    public function get(int $id): array
    {
        return $this->client->get('/country/get', ['id' => $id]);
    }

    /**
     * GET /country/lists
     *
     * Filters: search (by country name or ISO-2/ISO-3 code), page,
     * per_page (defaults to 15, supports up to 100).
     *
     * @param array<string, int|string> $filters
     * @return array{success: bool, countries?: array{data?: array<int, array<string, mixed>>}}
     */
    public function list(array $filters = []): array
    {
        return $this->client->get('/country/lists', $filters);
    }
}
