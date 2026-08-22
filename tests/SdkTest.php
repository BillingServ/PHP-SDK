<?php

declare(strict_types=1);

namespace BillingServ\Tests;

use BillingServ\BillingServ;
use BillingServ\Client;
use BillingServ\Exception\ApiException;
use BillingServ\Exception\AuthenticationException;
use BillingServ\Exception\NetworkException;
use BillingServ\Exception\RateLimitException;
use BillingServ\Exception\ValidationException;
use PHPUnit\Framework\TestCase;

final class SdkTest extends TestCase
{
    private FakeTransport $transport;

    private BillingServ $sdk;

    protected function setUp(): void
    {
        $this->transport = new FakeTransport();
        $this->sdk = new BillingServ('test-api-key', ['transport' => ($this->transport)()]);
    }

    public function test_get_request_sends_bearer_auth_and_query(): void
    {
        $this->sdk->customers->get(5);

        $request = $this->transport->requests[0];
        self::assertSame('GET', $request['method']);
        self::assertSame(Client::DEFAULT_BASE_URI.'/customer/get?id=5', $request['url']);
        self::assertContains('Authorization: Bearer test-api-key', $request['headers']);
        self::assertSame('application/json', $this->header($request, 'Accept'));
        self::assertStringStartsWith('billingserv-php-sdk/', $this->header($request, 'User-Agent'));
        self::assertNull($request['body']);
    }

    public function test_post_request_encodes_json_and_idempotency_key(): void
    {
        $this->sdk->invoices->createInvoice(
            ['customer_id' => 4, 'duedate' => '01-31-2027'],
            idempotencyKey: 'order-42-attempt-1'
        );

        $request = $this->transport->requests[0];
        self::assertSame('POST', $request['method']);
        self::assertSame('/invoice/create-invoice', str_replace(Client::DEFAULT_BASE_URI, '', $request['url']));
        self::assertSame('application/json', $this->header($request, 'Content-Type'));
        self::assertSame('order-42-attempt-1', $this->header($request, 'Idempotency-Key'));
        self::assertSame('{"customer_id":4,"duedate":"01-31-2027"}', $request['body']);
    }

    public function test_decodes_successful_response_body(): void
    {
        $this->transport = new FakeTransport();
        $client = FakeTransport::client(($this->transport)([['status' => 200, 'body' => '{"success":true,"url":"https:\/\/x.test\/c"}']]));

        $result = (new \BillingServ\Resource\Checkout($client))->create(['callback_url' => 'https://app.test/done']);

        self::assertTrue($result['success']);
        self::assertSame('https://x.test/c', $result['url']);
    }

    public function test_401_throws_authentication_exception(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->runWithResponses([['status' => 401, 'body' => '{"success":false,"errors":"Invalid API Key"}']]);
        $this->sdk->customers->get(1);
    }

    public function test_422_maps_validation_errors(): void
    {
        try {
            $this->runWithResponses([[
                'status' => 422,
                'body' => '{"success":false,"errors":{"id":["The id field is required."]},"code":"validation_failed","request_id":"req-123"}',
            ]]);
            $this->sdk->customers->get(1);
            self::fail('ValidationException expected');
        } catch (ValidationException $e) {
            self::assertSame(422, $e->getStatus());
            self::assertSame('The id field is required.', $e->getMessage());
            self::assertSame(['id' => ['The id field is required.']], $e->getErrors());
            self::assertSame('validation_failed', $e->getErrorCode());
            self::assertSame('req-123', $e->getRequestId());
        }
    }

    public function test_429_includes_retry_after_header(): void
    {
        try {
            $this->runWithResponses([[ 'status' => 429, 'headers' => ['Retry-After' => '17'], 'body' => '' ]]);
            $this->sdk->domains->lookup('billingserv');
            self::fail('RateLimitException expected');
        } catch (RateLimitException $e) {
            self::assertSame(429, $e->getStatus());
            self::assertSame(17, $e->getRetryAfter());
        }
    }

    public function test_unmapped_error_status_throws_base_api_exception(): void
    {
        $this->expectException(ApiException::class);
        $this->runWithResponses([['status' => 502, 'body' => '{"success":false,"errors":["Unable to check domain availability right now."]}']]);
        $this->sdk->domains->lookup('billingserv');
    }

    public function test_transport_failure_is_wrapped_in_network_exception(): void
    {
        $client = new Client('test-api-key', [
            'transport' => fn () => throw new NetworkException('HTTP request failed: connection refused'),
        ]);

        $this->expectException(NetworkException::class);
        $client->get('/country/lists');
    }

    public function test_resources_are_wired_to_shared_client(): void
    {
        $this->runWithResponses(null);
        $this->sdk->usage->get(3, 9);

        $request = $this->transport->requests[0];
        self::assertSame(Client::DEFAULT_BASE_URI.'/meter/3/get/9', $request['url']);
    }

    public function test_currencies_list_hits_currency_endpoint(): void
    {
        $this->runWithResponses([[
            'status' => 200,
            'body' => '{"success":true,"currencies":[{"id":4,"name":"British Pounds","short_name":"GBP","symbol":"&pound;","conversion":1}]}',
        ]]);

        $result = $this->sdk->currencies->list();

        self::assertTrue($result['success']);
        self::assertSame('GBP', $result['currencies'][0]['short_name']);
        self::assertSame(Client::DEFAULT_BASE_URI.'/currency/lists', $this->transport->requests[0]['url']);
    }

    public function test_package_option_get_returns_option_with_values(): void
    {
        $this->runWithResponses([[
            'status' => 200,
            'body' => '{"success":true,"package_option":{"id":3,"display_name":"RAM","required":1,"type":0,'
                .'"values":[{"id":30,"option_id":3,"display_name":"4 GB","cycle_type":5,"price":12.5,"fee":1.25}]}}',
        ]]);

        $result = $this->sdk->packageOptions->get(3);

        self::assertTrue($result['success']);
        self::assertSame(0, $result['package_option']['type']);          // 0 = select dropdown
        self::assertSame('4 GB', $result['package_option']['values'][0]['display_name']);
        self::assertSame('/package/option/get?id=3', str_replace(Client::DEFAULT_BASE_URI, '', $this->transport->requests[0]['url']));
    }

    public function test_checkout_create_sends_options_payload(): void
    {
        $this->runWithResponses(null);

        $this->sdk->checkout->create([
            'package_id' => 66,
            'cycle_id' => 87,
            'callback_url' => 'https://app.test/thanks',
            'options' => ['id' => [3], 'value' => ['4 GB'], 'amount' => [0], 'cycle_type' => [5]],
        ]);

        self::assertStringContainsString('"options":{"id":[3],"value":["4 GB"],"amount":[0],"cycle_type":[5]}', (string) $this->transport->requests[0]['body']);
    }

    public function test_order_and_invoice_status_definitions(): void
    {
        $this->runWithResponses([
            ['status' => 200, 'body' => '{"success":true,"statuses":[{"status":"0","status_key":"RECENT","status_label":"Recent","description":"Awaiting processing"}]}'],
            ['status' => 200, 'body' => '{"success":true,"statuses":[{"status":"1","status_key":"PAID","status_label":"Paid","description":"The invoice has been paid."}]}'],
        ]);

        $orderStatuses = $this->sdk->orders->statuses();
        $invoiceStatuses = $this->sdk->invoices->statuses();

        self::assertSame('Recent', $orderStatuses['statuses'][0]['status_label']);
        self::assertSame(Client::DEFAULT_BASE_URI.'/order/statuses', $this->transport->requests[0]['url']);
        self::assertSame('Paid', $invoiceStatuses['statuses'][0]['status_label']);
        self::assertSame(Client::DEFAULT_BASE_URI.'/invoice/statuses', $this->transport->requests[1]['url']);
    }

    public function test_country_and_county_search_filters_are_sent(): void
    {
        $this->runWithResponses([
            ['status' => 200, 'body' => '{"success":true,"countries":{"data":[{"id":81,"name":"United Kingdom","iso2":"GB"}]}}'],
            ['status' => 200, 'body' => '{"success":true,"counties":{"data":[{"id":123,"country_id":81,"name":"Lancashire","code":"LAN"}]}}'],
        ]);

        $countries = $this->sdk->countries->list(['search' => 'united', 'per_page' => 10]);
        $counties = $this->sdk->counties->listByCountry(81, ['search' => 'lanc', 'per_page' => 10]);

        self::assertSame('United Kingdom', $countries['countries']['data'][0]['name']);
        self::assertSame(Client::DEFAULT_BASE_URI.'/country/lists?search=united&per_page=10', $this->transport->requests[0]['url']);
        self::assertSame('Lancashire', $counties['counties']['data'][0]['name']);
        self::assertSame(
            Client::DEFAULT_BASE_URI.'/county/lists-by-country?country_id=81&search=lanc&per_page=10',
            $this->transport->requests[1]['url']
        );
    }

    /**
     * @param array<int, array{status: int, body?: string, headers?: array<string,string>}>|null $responses
     */
    /**
     * @param array<int, array{status: int, body?: string, headers?: array<string,string>}>|null $responses
     */
    private function runWithResponses(?array $responses): void
    {
        $this->sdk = new BillingServ('test-api-key', ['transport' => ($this->transport)($responses)]);
    }

    private function header(array $request, string $name): ?string
    {
        foreach ($request['headers'] as $line) {
            if (str_starts_with($line, $name.':')) {
                return trim(substr($line, strlen($name) + 1));
            }
        }

        return null;
    }
}
