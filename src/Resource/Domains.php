<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/** Domain availability checks (rate limited to 30 requests/minute). POST /domain/lookup. */
final class Domains extends Resource
{
    /**
     * Check availability across enabled extensions. Send the name without extension, e.g. "billingserv".
     *
     * @return array{success: bool, domain?: string, registrar?: string, results?: array<int, array{domain: string, extension: string, available: bool, premium: bool}>}
     */
    public function lookup(string $domain, ?int $limit = null): array
    {
        $data = ['domain' => $domain];
        if ($limit !== null) {
            $data['limit'] = $limit;
        }

        return $this->client->post('/domain/lookup', $data);
    }
}
