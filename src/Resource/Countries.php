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

    /** GET /country/lists */
    public function list(): array
    {
        return $this->client->get('/country/lists');
    }
}
