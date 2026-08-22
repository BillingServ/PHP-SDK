<?php

declare(strict_types=1);

namespace BillingServ\Tests;

use BillingServ\Webhook;
use PHPUnit\Framework\TestCase;

final class WebhookTest extends TestCase
{
    private const SECRET = 'whsec_test_123';

    public function test_verify_accepts_valid_signature(): void
    {
        $payload = '{"id":"evt_1","type":"invoice.paid","data":{}}';
        $timestamp = time();

        self::assertTrue(Webhook::verify(
            $payload,
            't='.$timestamp.',v1='.$this->sign($timestamp, $payload),
            self::SECRET
        ));
    }

    public function test_verify_rejects_tampered_payload(): void
    {
        $payload = '{"id":"evt_1"}';
        $timestamp = time();
        $signature = 't='.$timestamp.',v1='.$this->sign($timestamp, '{"id":"evt_2"}');

        self::assertFalse(Webhook::verify($payload, $signature, self::SECRET));
    }

    public function test_verify_rejects_stale_timestamp(): void
    {
        $payload = '{}';
        $timestamp = time() - 3600;
        $signature = 't='.$timestamp.',v1='.$this->sign($timestamp, $payload);

        self::assertFalse(Webhook::verify($payload, $signature, self::SECRET));
    }

    public function test_verify_rejects_stale_timestamp_even_with_zero_tolerance(): void
    {
        $payload = '{}';
        $timestamp = time() - 3600;
        $signature = 't='.$timestamp.',v1='.$this->sign($timestamp, $payload);

        $this->expectException(\InvalidArgumentException::class);
        Webhook::verify($payload, $signature, self::SECRET, 0);
    }

    public function test_verify_rejects_negative_tolerance(): void
    {
        $payload = '{}';
        $timestamp = time();
        $signature = 't='.$timestamp.',v1='.$this->sign($timestamp, $payload);

        $this->expectException(\InvalidArgumentException::class);
        Webhook::verify($payload, $signature, self::SECRET, -5);
    }

    public function test_verify_rejects_malformed_headers(): void
    {
        self::assertFalse(Webhook::verify('{}', 'garbage', self::SECRET));
        self::assertFalse(Webhook::verify('{}', '', self::SECRET));
        self::assertFalse(Webhook::verify('{}', 'v1=abc', self::SECRET));
    }

    public function test_parse_returns_envelope(): void
    {
        $envelope = Webhook::parse('{"id":"evt_1","type":"payment.succeeded","version":"1","data":{"amount":10}}');

        self::assertSame('evt_1', $envelope['id']);
        self::assertSame('payment.succeeded', $envelope['type']);
    }

    public function test_parse_throws_on_invalid_json(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Webhook::parse('not-json');
    }

    private function sign(int $timestamp, string $payload): string
    {
        return hash_hmac('sha256', $timestamp.'.'.$payload, self::SECRET);
    }
}
