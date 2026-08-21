<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/** Module activation and configuration. */
final class Modules extends Resource
{
    /** POST /module/activate-deactivate */
    public function setStatus(string $module, bool|int $status): array
    {
        return $this->client->post('/module/activate-deactivate', [
            'module' => $module,
            'status' => (int) (bool) $status,
        ]);
    }

    /** GET /module/get-module-configuration */
    public function getConfiguration(string $module): array
    {
        return $this->client->get('/module/get-module-configuration', ['module' => $module]);
    }
}
