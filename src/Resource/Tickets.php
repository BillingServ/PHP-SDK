<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/** Support tickets. */
final class Tickets extends Resource
{
    /**
     * POST /ticket/create.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): array
    {
        return $this->client->post('/ticket/create', $data);
    }

    /**
     * POST /ticket/update.
     *
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): array
    {
        return $this->client->post('/ticket/update', ['id' => $id] + $data);
    }

    /** POST /ticket/reply */
    public function reply(int $id, string $message): array
    {
        return $this->client->post('/ticket/reply', ['id' => $id, 'message' => $message]);
    }

    /** GET /ticket/get */
    public function get(int $id): array
    {
        return $this->client->get('/ticket/get', ['id' => $id]);
    }

    /** GET /ticket/lists. Filters: user_id, status, priority, support_department, assignee_by. */
    public function list(array $filters = []): array
    {
        return $this->client->get('/ticket/lists', $filters);
    }

    /** POST /ticket/delete */
    public function delete(int $id): array
    {
        return $this->client->post('/ticket/delete', ['id' => $id]);
    }
}
