<?php

declare(strict_types=1);

namespace BillingServ\Resource;

/** Sales and revenue reports. */
final class Reports extends Resource
{
    /** GET /report/annual-sales */
    public function annualSales(): array
    {
        return $this->client->get('/report/annual-sales');
    }

    /** GET /report/revenue-trend */
    public function revenueTrend(): array
    {
        return $this->client->get('/report/revenue-trend');
    }

    /** GET /report/sales-by-customer. Params: timeframe, date, per_page. */
    public function salesByCustomer(array $params = []): array
    {
        return $this->client->get('/report/sales-by-customer', $params);
    }

    /** GET /report/sales-by-staff. Params: timeframe, date. */
    public function salesByStaff(array $params = []): array
    {
        return $this->client->get('/report/sales-by-staff', $params);
    }

    /** GET /report/package-leaderboard */
    public function packageLeaderboard(?int $perPage = null): array
    {
        return $this->client->get('/report/package-leaderboard', array_filter(['per_page' => $perPage]));
    }

    /** GET /report/customer-credit */
    public function customerCredit(?int $perPage = null): array
    {
        return $this->client->get('/report/customer-credit', array_filter(['per_page' => $perPage]));
    }

    /** GET /report/customer-debt */
    public function customerDebt(?int $perPage = null): array
    {
        return $this->client->get('/report/customer-debt', array_filter(['per_page' => $perPage]));
    }

    /** GET /report/customer-invoice */
    public function customerInvoices(?int $perPage = null): array
    {
        return $this->client->get('/report/customer-invoice', array_filter(['per_page' => $perPage]));
    }

    /** GET /report/customer-receipt */
    public function customerReceipts(?int $perPage = null): array
    {
        return $this->client->get('/report/customer-receipt', array_filter(['per_page' => $perPage]));
    }
}
