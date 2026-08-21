<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/** Order and lifecycle management. */
final class Orders extends Resource
{
    /** GET /order/get-orders */
    public function list(?int $perPage = null): array
    {
        return $this->client->get('/order/get-orders', array_filter(['per_page' => $perPage]));
    }

    /** GET /order/get-orders-by-status */
    public function listByStatus(string $status, ?int $perPage = null): array
    {
        $query = ['status' => $status];
        if ($perPage !== null) {
            $query['per_page'] = $perPage;
        }

        return $this->client->get('/order/get-orders-by-status', $query);
    }

    /**
     * POST /order/add-order. Supports Idempotency-Key.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data, ?string $idempotencyKey = null): array
    {
        return $this->client->post('/order/add-order', $data, $idempotencyKey);
    }

    /** POST /order/accept-order */
    public function accept(int $orderId): array
    {
        return $this->client->post('/order/accept-order', ['order_id' => $orderId]);
    }

    /** POST /order/pending-order */
    public function markPending(int $orderId): array
    {
        return $this->client->post('/order/pending-order', ['order_id' => $orderId]);
    }

    /** POST /order/fraud-order */
    public function markFraud(int $orderId, ?string $fraudlabsJson = null): array
    {
        $data = ['order_id' => $orderId];
        if ($fraudlabsJson !== null) {
            $data['fraudlabs_json'] = $fraudlabsJson;
        }

        return $this->client->post('/order/fraud-order', $data);
    }

    /** POST /order/cancel-order */
    public function cancel(int $orderId): array
    {
        return $this->client->post('/order/cancel-order', ['order_id' => $orderId]);
    }

    /** POST /order/delete-order */
    public function delete(int $orderId): array
    {
        return $this->client->post('/order/delete-order', ['order_id' => $orderId]);
    }

    /** POST /order/change-package */
    public function changePackage(int $orderId, int $packageId, int $cycleId): array
    {
        return $this->client->post('/order/change-package', [
            'order_id' => $orderId,
            'package_id' => $packageId,
            'cycle_id' => $cycleId,
        ]);
    }

    /** GET /order/available-package-changes */
    public function availablePackageChanges(int $orderId): array
    {
        return $this->client->get('/order/available-package-changes', ['order_id' => $orderId]);
    }

    /** GET /order/preview-package-change */
    public function previewPackageChange(int $orderId, int $packageId, int $cycleId): array
    {
        return $this->client->get('/order/preview-package-change', [
            'order_id' => $orderId,
            'package_id' => $packageId,
            'cycle_id' => $cycleId,
        ]);
    }

    /** GET /order/check-fraud */
    public function checkFraud(int $orderId, ?string $cardNumber = null): array
    {
        $query = ['order_id' => $orderId];
        if ($cardNumber !== null) {
            $query['paymentMethod.number'] = $cardNumber;
        }

        return $this->client->get('/order/check-fraud', $query);
    }
}
