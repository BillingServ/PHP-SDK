<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/** Staff accounts, tax classes/zones and invoice settings. */
final class Settings extends Resource
{
    /**
     * POST /setting/create-staff.
     *
     * @param array<string, mixed> $data
     */
    public function createStaff(array $data): array
    {
        return $this->client->post('/setting/create-staff', $data);
    }

    /**
     * POST /setting/update-staff.
     *
     * @param array<string, mixed> $data
     */
    public function updateStaff(int $userId, array $data): array
    {
        return $this->client->post('/setting/update-staff', ['user_id' => $userId] + $data);
    }

    /** POST /setting/delete-staff */
    public function deleteStaff(int $userId): array
    {
        return $this->client->post('/setting/delete-staff', ['user_id' => $userId]);
    }

    /** GET /setting/get-staff */
    public function getStaff(int $userId): array
    {
        return $this->client->get('/setting/get-staff', ['user_id' => $userId]);
    }

    /** GET /setting/lists-staff */
    public function listStaff(?int $perPage = null): array
    {
        return $this->client->get('/setting/lists-staff', array_filter(['per_page' => $perPage]));
    }

    /**
     * POST /setting/create-tax-class.
     *
     * @param array<string, mixed> $data
     */
    public function createTaxClass(array $data): array
    {
        return $this->client->post('/setting/create-tax-class', $data);
    }

    /**
     * POST /setting/update-tax-class.
     *
     * @param array<string, mixed> $data
     */
    public function updateTaxClass(int $classId, array $data): array
    {
        return $this->client->post('/setting/update-tax-class', ['class_id' => $classId] + $data);
    }

    /** POST /setting/delete-tax-class */
    public function deleteTaxClass(int $classId): array
    {
        return $this->client->post('/setting/delete-tax-class', ['class_id' => $classId]);
    }

    /** GET /setting/get-tax-class */
    public function getTaxClass(int $classId): array
    {
        return $this->client->get('/setting/get-tax-class', ['class_id' => $classId]);
    }

    /** GET /setting/lists-tax-class */
    public function listTaxClasses(?int $perPage = null): array
    {
        return $this->client->get('/setting/lists-tax-class', array_filter(['per_page' => $perPage]));
    }

    /**
     * POST /setting/create-tax-zone.
     *
     * @param array<string, mixed> $data
     */
    public function createTaxZone(array $data): array
    {
        return $this->client->post('/setting/create-tax-zone', $data);
    }

    /**
     * POST /setting/update-tax-zone.
     *
     * @param array<string, mixed> $data
     */
    public function updateTaxZone(int $zoneId, array $data): array
    {
        return $this->client->post('/setting/update-tax-zone', ['zone_id' => $zoneId] + $data);
    }

    /** POST /setting/delete-tax-zone */
    public function deleteTaxZone(int $zoneId): array
    {
        return $this->client->post('/setting/delete-tax-zone', ['zone_id' => $zoneId]);
    }

    /** GET /setting/get-tax-zone */
    public function getTaxZone(int $zoneId): array
    {
        return $this->client->get('/setting/get-tax-zone', ['zone_id' => $zoneId]);
    }

    /** GET /setting/lists-tax-zone */
    public function listTaxZones(?int $perPage = null): array
    {
        return $this->client->get('/setting/lists-tax-zone', array_filter(['per_page' => $perPage]));
    }

    /** GET /setting/invoice */
    public function getInvoiceSettings(): array
    {
        return $this->client->get('/setting/invoice');
    }

    /**
     * POST /setting/invoice.
     *
     * @param array<string, mixed> $data
     */
    public function updateInvoiceSettings(array $data): array
    {
        return $this->client->post('/setting/invoice', $data);
    }
}
