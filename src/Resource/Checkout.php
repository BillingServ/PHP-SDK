<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/**
 * Hosted checkout links. POST /checkout/create.
 */
final class Checkout extends Resource
{
    /**
     * Create a short-lived hosted checkout URL for a package, one domain, or up to 50 domains.
     * Provide exactly one of package_id+cycle_id, domain+years, or domains[]; callback_url is required.
     *
     * @param array<string, mixed> $data
     * @return array{success: bool, message?: string, url?: string}
     */
    public function create(array $data, ?string $idempotencyKey = null): array
    {
        return $this->client->post('/checkout/create', $data, $idempotencyKey);
    }
}
