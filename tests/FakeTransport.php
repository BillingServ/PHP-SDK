<?php

declare(strict_types=1);

namespace BillingServ\Tests;

use BillingServ\Client;

/** Records requests and returns canned responses instead of hitting the network. */
final class FakeTransport
{
    public array $requests = [];

    /**
     * @param array<int, array{status: int, body?: string}>|null $responses
     * @return callable(array): array{0: int, 1: array<string, string>, 2: string}
     */
    public function __invoke(?array $responses = null): callable
    {
        return function (array $request) use ($responses): array {
            $this->requests[] = $request;

            $response = $responses[$this->position()] ?? ['status' => 200, 'body' => '{"success":true}'];

            return [
                $response['status'],
                array_key_exists('headers', $response) ? $response['headers'] : [],
                $response['body'] ?? '{"success":true}',
            ];
        };
    }

    private function position(): int
    {
        return count($this->requests) - 1;
    }

    public static function client(callable $transport): Client
    {
        return new Client('test-api-key', 'https://sdk.test', ['transport' => $transport]);
    }
}
