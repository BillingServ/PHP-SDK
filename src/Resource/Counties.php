<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/** Counties. */
final class Counties extends Resource
{
    /**
     * GET /county/lists-by-country
     *
     * Filters: search (by county name or code), page,
     * per_page (defaults to 15, supports up to 100).
     *
     * @param array<string, int|string> $filters
     * @return array{success: bool, counties?: array{data?: array<int, array<string, mixed>>}}
     */
    public function listByCountry(int $countryId, array $filters = []): array
    {
        return $this->client->get('/county/lists-by-country',
            ['country_id' => $countryId] + array_filter($filters));
    }
}
