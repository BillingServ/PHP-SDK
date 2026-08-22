<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/** Invoices and quotes. */
final class Invoices extends Resource
{
    /**
     * GET /invoice/lists. Status: UNPAID, PAID, OVERDUE, REFUNDED, CANCELED, PENDING or ALL.
     */
    public function list(string $status, ?int $perPage = null): array
    {
        $query = ['status' => $status];
        if ($perPage !== null) {
            $query['per_page'] = $perPage;
        }

        return $this->client->get('/invoice/lists', $query);
    }

    /**
     * GET /invoice/statuses
     *
     * Definitions mapping the numeric status on invoice records to their API key
     * and human label, e.g. {status: "0", status_key: "UNPAID", status_label: "Unpaid"}.
     *
     * @return array{success: bool, statuses?: array<int, array{status: string, status_key: string, status_label: string, description: string}>}
     */
    public function statuses(): array
    {
        return $this->client->get('/invoice/statuses');
    }

    /**
     * POST /invoice/create-invoice. Supports Idempotency-Key. duedate format: MM-DD-YYYY.
     *
     * @param array<string, mixed> $data
     */
    public function createInvoice(array $data, ?string $idempotencyKey = null): array
    {
        return $this->client->post('/invoice/create-invoice', $data, $idempotencyKey);
    }

    /**
     * POST /invoice/update-invoice. Supports Idempotency-Key. duedate format: MM-DD-YYYY.
     *
     * @param array<string, mixed> $data
     */
    public function updateInvoice(array $data, ?string $idempotencyKey = null): array
    {
        return $this->client->post('/invoice/update-invoice', $data, $idempotencyKey);
    }

    /** POST /invoice/delete-invoice */
    public function deleteInvoice(int $invoiceId): array
    {
        return $this->client->post('/invoice/delete-invoice', ['invoice_id' => $invoiceId]);
    }

    /** POST /invoice/capture-payment */
    public function capturePayment(int $invoiceId): array
    {
        return $this->client->post('/invoice/capture-payment', ['invoice_id' => $invoiceId]);
    }

    /** POST /invoice/send-invoice */
    public function send(int $invoiceId): array
    {
        return $this->client->post('/invoice/send-invoice', ['invoice_id' => $invoiceId]);
    }

    /** POST /invoice/send-invoice-reminder */
    public function sendReminder(int $invoiceId): array
    {
        return $this->client->post('/invoice/send-invoice-reminder', ['invoice_id' => $invoiceId]);
    }

    /** GET /invoice/get-payment-method */
    public function getPaymentMethod(int $invoiceId): array
    {
        return $this->client->get('/invoice/get-payment-method', ['invoice_id' => $invoiceId]);
    }

    /** GET /invoice/get-transactions */
    public function getTransactions(int $invoiceId): array
    {
        return $this->client->get('/invoice/get-transactions', ['invoice_id' => $invoiceId]);
    }

    /**
     * POST /invoice/update-transaction.
     *
     * @param array<string, mixed> $data
     */
    public function updateTransaction(array $data): array
    {
        return $this->client->post('/invoice/update-transaction', $data);
    }

    /**
     * POST /invoice/create-quote. Supports Idempotency-Key. duedate format: MM-DD-YYYY.
     *
     * @param array<string, mixed> $data
     */
    public function createQuote(array $data, ?string $idempotencyKey = null): array
    {
        return $this->client->post('/invoice/create-quote', $data, $idempotencyKey);
    }

    /** POST /invoice/accept-quote */
    public function acceptQuote(int $invoiceId): array
    {
        return $this->client->post('/invoice/accept-quote', ['invoice_id' => $invoiceId]);
    }

    /** POST /invoice/delete-quote */
    public function deleteQuote(int $invoiceId): array
    {
        return $this->client->post('/invoice/delete-quote', ['invoice_id' => $invoiceId]);
    }
}
