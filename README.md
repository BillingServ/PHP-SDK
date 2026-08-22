# BillingServ PHP SDK

The official PHP SDK for the [BillingServ v2 API](https://www.billingserv.com/docs/api-reference/introduction).
It covers customers, invoices, quotes, orders, packages, hosted checkout, domain availability,
usage metering, reports, settings, support tickets, the VPN module and software licensing. Full API
documentation lives at [billingserv.com/docs/developers](https://www.billingserv.com/docs/developers).

## Requirements

- PHP 8.3+ with the `curl` and `json` extensions.

## Installation

```bash
composer require billingserv/php-sdk
```

## Getting started

```php
use BillingServ\BillingServ;

$billingserv = new BillingServ('your-bearer-api-key');
// Custom install: new BillingServ($key, ['base_uri' => 'https://your-install.com/api/v2', 'timeout' => 15]);
```

### Configuration (.env)

The SDK itself takes your key and base URL as constructor arguments. The bundled
demo site (`demo/`) reads them from a `.env` file instead: copy `demo/.env.example`
to `demo/.env` and fill in:

| Key | Required | Purpose |
| --- | --- | --- |
| `BILLINGSERV_API_KEY` | Yes | Your **merchant** bearer token from the API Information page. Powers packages, checkout, currencies, statuses and the account page's order/invoice lists |
| `BILLINGSERV_BASE_URI` | Yes | Your install, e.g. `https://yourdomain.com/api/v2`. Defaults to the public demo server |
| `APP_URL` | Recommended | Public URL of the demo folder, used as the checkout return address. Detected automatically if empty; set it explicitly behind proxies/CDNs |
| `BILLINGSERV_WEBHOOK_SECRET` | For webhooks | Signing secret for `webhook.php`. Without it, webhook testing needs `?secret=` in the URL instead |

Notes:

- The merchant key must have permission for the endpoints the site uses:
  packages, package options, checkout, countries, counties, currencies,
  order/invoice statuses and the order/invoice lists.
- Customers do **not** need an API key. They sign up or log in with their email
  and password (`/customer/check`); their account page pulls their orders and
  invoices through your merchant key, filtered to their customer ID.


### Customers

```php
$customer = $billingserv->customers->create([
    'name' => 'Jane Doe',
    'address_1' => '1 High Street',
    'city' => 'Manchester',
    'county_id' => 1231,
    'country_id' => 81,
    'postal_code' => 'M1 1AA',
    'phone' => '03302207048',
    'username' => 'jane@example.com',
    'password' => 'secret',
], idempotencyKey: 'signup-'.$reference);   // safe to retry for 24h

$billingserv->customers->addCredit(4, 10.00);
$billingserv->customers->list(['search' => 'jane', 'per_page' => 25]);
```

### Invoices & quotes

```php
$billingserv->invoices->createInvoice([
    'customer_id' => 4,
    'duedate' => '01-31-2027',              // MM-DD-YYYY
    'record' => [
        'item' => ['Hosting'],
        'description' => ['Business hosting'],
        'price' => ['145.00'],
        'quantity' => ['1'],
        'tax_class' => ['0'],
        'tax' => ['0'],
    ],
]);

$invoices = $billingserv->invoices->list('UNPAID', perPage: 50);
$billingserv->invoices->capturePayment(271);
```

### Hosted checkout & domains

```php
$result = $billingserv->checkout->create([
    'package_id' => 12,
    'cycle_id' => 3,
    'callback_url' => 'https://app.example.com/thanks',
    'customer_id' => 4,
]);
header('Location: '.$result['url']);

$availability = $billingserv->domains->lookup('billingserv'); // checks every enabled extension
foreach ($availability['results'] ?? [] as $r) {
    echo $r['domain'], $r['available'] ? ' is available' : ' is taken', "\n";
}
```

### Orders

```php
$billingserv->orders->create([
    'customer_id' => 4,
    'package_id' => 12,
    'cycle_id' => 3,
    'price' => 145.00,
], idempotencyKey: 'order-42');

$billingserv->orders->accept(42);
$billingserv->orders->previewPackageChange(orderId: 42, packageId: 13, cycleId: 3);
```

The other resources work the same way: `packages`, `packageGroups`, `packageOptions`,
`marketing`, `reports`, `settings`, `tickets`, `vpn`, `licenses`, `usage`, `modules`, `countries` and
`counties`. Every method maps to an endpoint in the
[API reference](https://www.billingserv.com/docs/api-reference/introduction).

## Error handling

Anything that goes wrong throws an exception. Successful responses come back as plain arrays.

```php
use BillingServ\Exception\{AuthenticationException, RateLimitException, ValidationException};

try {
    $billingserv->customers->get(999);
} catch (ValidationException $e) {
    $e->getErrors();       // field => messages (or a string)
} catch (AuthenticationException $e) {
    // invalid API key
} catch (RateLimitException $e) {
    sleep($e->getRetryAfter() ?? 30);
}
```

`BillingServ\Exception\ApiException` covers every other HTTP error and exposes `getStatus()`,
`getErrors()`, `getErrorCode()`, `getRequestId()` and `getResponse()`. Network failures throw
`NetworkException`.

## Webhooks

Verify deliveries with your Webhook Signing Secret **before parsing the body**:

```php
use BillingServ\Webhook;

$payload = file_get_contents('php://input');

if (!Webhook::verify($payload, $_SERVER['HTTP_BILLINGSERV_SIGNATURE'] ?? '', $signingSecret)) {
    http_response_code(400);
    exit;
}

$event = Webhook::parse($payload); // {id, type, version, created_at, data}
// enqueue/persist by $event['id'] (deliveries may retry), then respond within 10s
```

## Testing

```bash
composer install
composer test
```

## License

MIT
