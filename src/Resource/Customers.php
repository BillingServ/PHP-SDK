<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/** Customer management. */
final class Customers extends Resource
{
    /** GET /customer/get */
    public function get(int $id): array
    {
        return $this->client->get('/customer/get', ['id' => $id]);
    }

    /** GET /customer/lists. Filters: page, per_page, search, sort_by, sort_dir. */
    public function list(array $filters = []): array
    {
        return $this->client->get('/customer/lists', $filters);
    }

    /**
     * POST /customer/create. Supports Idempotency-Key.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data, ?string $idempotencyKey = null): array
    {
        return $this->client->post('/customer/create', $data, $idempotencyKey);
    }

    /**
     * POST /customer/update.
     *
     * @param array<string, mixed> $data
     */
    public function update(array $data): array
    {
        return $this->client->post('/customer/update', $data);
    }

    /** POST /customer/delete */
    public function delete(int $id): array
    {
        return $this->client->post('/customer/delete', ['id' => $id]);
    }

    /** POST /customer/add-credit */
    public function addCredit(int $id, float $credit): array
    {
        return $this->client->post('/customer/add-credit', ['id' => $id, 'credit' => $credit]);
    }

    /** GET /customer/get-credit */
    public function getCredit(int $id): array
    {
        return $this->client->get('/customer/get-credit', ['id' => $id]);
    }

    /** POST /customer/add-note */
    public function addNote(int $id, string $note): array
    {
        return $this->client->post('/customer/add-note', ['id' => $id, 'note' => $note]);
    }

    /** POST /customer/delete-pay-method */
    public function deletePayMethod(int $id): array
    {
        return $this->client->post('/customer/delete-pay-method', ['id' => $id]);
    }

    /** GET /customer/reset-password */
    public function resetPassword(string $email): array
    {
        return $this->client->get('/customer/reset-password', ['email' => $email]);
    }

    /** POST /customer/check. Validates customer credentials (no API key required). */
    public function checkCredentials(string $username, string $password): array
    {
        return $this->client->post('/customer/check', ['username' => $username, 'password' => $password]);
    }
}
