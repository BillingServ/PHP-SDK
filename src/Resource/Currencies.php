<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/** Currencies enabled on this install. */
final class Currencies extends Resource
{
    /**
     * GET /currency/lists
     *
     * Each currency: id, name, short_name (ISO code), symbol (HTML entity,
     * e.g. "&euro;"), conversion rate and position.
     *
     * @return array{success: bool, currencies?: array<int, array<string, mixed>>}
     */
    public function list(): array
    {
        return $this->client->get('/currency/lists');
    }
}
