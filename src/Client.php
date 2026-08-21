<?php

declare(strict_types=1);

namespace BillingServ;

use BillingServ\Exception\ApiException;
use BillingServ\Exception\AuthenticationException;
use BillingServ\Exception\NetworkException;
use BillingServ\Exception\RateLimitException;
use BillingServ\Exception\ValidationException;

/**
 * Low-level HTTP client for the BillingServ v2 API.
 *
 * A custom transport callable may be injected via the `transport` option
 * (used for testing): fn(array $request): array{int, array<string,string>, string}
 * receiving ['method', 'url', 'headers', 'body'] and returning
 * [status, lowercase header map, raw body].
 */
final class Client
{
    public const VERSION = '1.0.0';
    public const DEFAULT_BASE_URI = 'https://demo.onlinebillingform.com/api/v2';

    /** @var callable(array): array{0: int, 1: array<string, string>, 2: string}|null */
    private $transport;

    /**
     * @param array{base_uri?: string, timeout?: int, transport?: callable} $options
     */
    public function __construct(
        private readonly string $apiKey,
        private readonly array $options = [],
    ) {
        $this->transport = $options['transport'] ?? null;
    }

    /** @return array<string, mixed> */
    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, ['query' => $query]);
    }

    /**
     * @param array<string, mixed> $body
     * @return array<string, mixed>
     */
    public function post(string $path, array $body = [], ?string $idempotencyKey = null): array
    {
        return $this->request('POST', $path, ['json' => $body, 'idempotency_key' => $idempotencyKey]);
    }

    /** @return array<string, mixed> */
    private function request(string $method, string $path, array $options = []): array
    {
        $url = rtrim($this->baseUri(), '/').'/'.ltrim($path, '/');
        if (!empty($options['query'])) {
            $url .= '?'.http_build_query($options['query']);
        }

        $headers = [
            'Accept: application/json',
            'Authorization: Bearer '.$this->apiKey,
            'User-Agent: billingserv-php-sdk/'.self::VERSION.' PHP/'.PHP_VERSION,
        ];

        $body = null;
        if ($method === 'POST') {
            $headers[] = 'Content-Type: application/json';
            $body = json_encode($options['json'] ?? [], JSON_THROW_ON_ERROR);
            if (!empty($options['idempotency_key'])) {
                $headers[] = 'Idempotency-Key: '.$options['idempotency_key'];
            }
        }

        [$status, $headersIn, $raw] = ($this->transport ?? $this->curlTransport(...))([
            'method' => $method,
            'url' => $url,
            'headers' => $headers,
            'body' => $body,
        ]);

        $responseHeaders = [];
        foreach ($headersIn as $name => $value) {
            $responseHeaders[strtolower((string) $name)] = (string) $value;
        }

        $data = [];
        if ($raw !== '') {
            try {
                $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
                $data = is_array($decoded) ? $decoded : [];
            } catch (\JsonException) {
                $data = null;
            }
        }

        if ($status >= 400) {
            throw self::toException($status, $responseHeaders, is_array($data) ? $data : [], is_array($data) ? null : $raw);
        }

        return $data;
    }

    private function baseUri(): string
    {
        return $this->options['base_uri'] ?? self::DEFAULT_BASE_URI;
    }

    /**
     * @param array<string, string> $headers
     */
    private static function toException(int $status, array $headers, array $data, ?string $rawBody): ApiException
    {
        $errors = $data['errors'] ?? ($rawBody !== null && $rawBody !== '' ? $rawBody : "HTTP {$status}");
        $errorCode = isset($data['code']) ? (string) $data['code'] : null;
        $requestId = isset($data['request_id']) ? (string) $data['request_id'] : null;

        return match (true) {
            $status === 401 => new AuthenticationException(self::firstMessage($errors), $status, $errors, $errorCode, $requestId, $data),
            $status === 422 => new ValidationException(self::firstMessage($errors), $status, $errors, $errorCode, $requestId, $data),
            $status === 429 => new RateLimitException(self::firstMessage($errors), $status, $errors, $errorCode, $requestId, $data, self::retryAfter($headers)),
            default => new ApiException(self::firstMessage($errors), $status, $errors, $errorCode, $requestId, $data),
        };
    }

    private static function firstMessage(string|array $errors): string
    {
        if (is_string($errors)) {
            return $errors;
        }

        foreach (new \RecursiveIteratorIterator(new \RecursiveArrayIterator($errors)) as $message) {
            return is_scalar($message) ? (string) $message : 'Unexpected API error';
        }

        return 'Unexpected API error';
    }

    /**
     * @param array<string, string> $headers
     */
    private static function retryAfter(array $headers): ?int
    {
        return isset($headers['retry-after']) ? max(0, (int) $headers['retry-after']) : null;
    }

    /**
     * @return array{0: int, 1: array<string, string>, 2: string}
     */
    private function curlTransport(array $request): array
    {
        $timeout = (int) ($this->options['timeout'] ?? 30);

        $handle = curl_init($request['url']);
        $responseHeaders = [];

        curl_setopt_array($handle, [
            CURLOPT_CUSTOMREQUEST => $request['method'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $request['headers'],
            CURLOPT_POSTFIELDS => $request['body'],
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => min(10, $timeout),
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HEADERFUNCTION => function ($handle, string $line) use (&$responseHeaders): int {
                if (str_contains($line, ':')) {
                    [$name, $value] = explode(':', $line, 2);
                    $responseHeaders[strtolower(trim($name))] = trim($value);
                }

                return strlen($line);
            },
        ]);

        $raw = curl_exec($handle);

        if ($raw === false) {
            $message = curl_error($handle);
            $errno = curl_errno($handle);
            curl_close($handle);
            throw new NetworkException("HTTP request failed: {$message}", $errno);
        }

        $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        curl_close($handle);

        return [$status, $responseHeaders, (string) $raw];
    }
}
