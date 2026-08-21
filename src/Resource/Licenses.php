<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/** Software licensing: activation, validation and deactivation. */
final class Licenses extends Resource
{
    /**
     * POST /license/activate. Data: license_key, instance, instance_type, plus optional environment fields.
     *
     * @param array<string, mixed> $data
     */
    public function activate(array $data): array
    {
        return $this->client->post('/license/activate', $data);
    }

    /**
     * POST /license/validate.
     *
     * @param array<string, mixed> $data
     */
    public function validate(array $data): array
    {
        return $this->client->post('/license/validate', $data);
    }

    /** POST /license/deactivate */
    public function deactivate(string $licenseKey, string $instance): array
    {
        return $this->client->post('/license/deactivate', ['license_key' => $licenseKey, 'instance' => $instance]);
    }
}
