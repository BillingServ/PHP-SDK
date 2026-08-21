<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/** Counties. */
final class Counties extends Resource
{
    /** GET /county/lists-by-country */
    public function listByCountry(int $countryId): array
    {
        return $this->client->get('/county/lists-by-country', ['country_id' => $countryId]);
    }
}
