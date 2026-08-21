<?php

declare(strict_types=1);

namespace BillingServ;

use BillingServ\Resource\Checkout;
use BillingServ\Resource\Counties;
use BillingServ\Resource\Countries;
use BillingServ\Resource\Currencies;
use BillingServ\Resource\Customers;
use BillingServ\Resource\Domains;
use BillingServ\Resource\Invoices;
use BillingServ\Resource\Licenses;
use BillingServ\Resource\Marketing;
use BillingServ\Resource\Modules;
use BillingServ\Resource\Orders;
use BillingServ\Resource\PackageGroups;
use BillingServ\Resource\PackageOptions;
use BillingServ\Resource\Packages;
use BillingServ\Resource\Reports;
use BillingServ\Resource\Settings;
use BillingServ\Resource\Tickets;
use BillingServ\Resource\Usage;
use BillingServ\Resource\Vpn;

/**
 * Entry point for the BillingServ v2 API SDK.
 *
 *     $billingserv = new \BillingServ\BillingServ('your-api-key');
 *     $customer = $billingserv->customers->get(1);
 */
final class BillingServ
{
    public readonly Checkout $checkout;
    public readonly Domains $domains;
    public readonly Countries $countries;
    public readonly Counties $counties;
    public readonly Currencies $currencies;
    public readonly Customers $customers;
    public readonly Invoices $invoices;
    public readonly Orders $orders;
    public readonly Packages $packages;
    public readonly PackageGroups $packageGroups;
    public readonly PackageOptions $packageOptions;
    public readonly Marketing $marketing;
    public readonly Usage $usage;
    public readonly Modules $modules;
    public readonly Reports $reports;
    public readonly Settings $settings;
    public readonly Tickets $tickets;
    public readonly Vpn $vpn;
    public readonly Licenses $licenses;

    private readonly Client $client;

    /**
     * @param array{base_uri?: string, timeout?: int, transport?: callable} $options
     */
    public function __construct(string $apiKey, array $options = [])
    {
        $this->client = new Client($apiKey, $options);

        foreach ([
            'checkout' => Checkout::class,
            'domains' => Domains::class,
            'countries' => Countries::class,
            'counties' => Counties::class,
            'currencies' => Currencies::class,
            'customers' => Customers::class,
            'invoices' => Invoices::class,
            'orders' => Orders::class,
            'packages' => Packages::class,
            'packageGroups' => PackageGroups::class,
            'packageOptions' => PackageOptions::class,
            'marketing' => Marketing::class,
            'usage' => Usage::class,
            'modules' => Modules::class,
            'reports' => Reports::class,
            'settings' => Settings::class,
            'tickets' => Tickets::class,
            'vpn' => Vpn::class,
            'licenses' => Licenses::class,
        ] as $property => $resource) {
            $this->{$property} = new $resource($this->client);
        }
    }

    /** The underlying HTTP client for raw requests. */
    public function client(): Client
    {
        return $this->client;
    }
}
