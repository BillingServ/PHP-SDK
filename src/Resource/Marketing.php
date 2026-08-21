<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/** Discounts. */
final class Marketing extends Resource
{
    /**
     * POST /marketing/create-discount.
     *
     * @param array<string, mixed> $data
     */
    public function createDiscount(array $data): array
    {
        return $this->client->post('/marketing/create-discount', $data);
    }

    /**
     * POST /marketing/update-discount.
     *
     * @param array<string, mixed> $data
     */
    public function updateDiscount(int $discountId, array $data): array
    {
        return $this->client->post('/marketing/update-discount', ['discount_id' => $discountId] + $data);
    }

    /** POST /marketing/delete-discount */
    public function deleteDiscount(int $discountId): array
    {
        return $this->client->post('/marketing/delete-discount', ['discount_id' => $discountId]);
    }

    /** GET /marketing/get-discount */
    public function getDiscount(int $discountId): array
    {
        return $this->client->get('/marketing/get-discount', ['discount_id' => $discountId]);
    }

    /** GET /marketing/lists. Filters: type, per_page. */
    public function list(array $filters = []): array
    {
        return $this->client->get('/marketing/lists', $filters);
    }
}
