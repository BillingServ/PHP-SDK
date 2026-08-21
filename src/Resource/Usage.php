<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/** Usage metering for metered billing. */
final class Usage extends Resource
{
    /**
     * POST /meter-event. Data: customer_id, order_id, name, qty, payload (optional).
     *
     * @param array<string, mixed> $data
     */
    public function recordEvent(array $data): array
    {
        return $this->client->post('/meter-event', $data);
    }

    /** GET /meter/{customer_id}/get/{order_id}. Returns usage meters and thresholds for an order. */
    public function get(int $customerId, int $orderId): array
    {
        return $this->client->get("/meter/{$customerId}/get/{$orderId}");
    }
}
