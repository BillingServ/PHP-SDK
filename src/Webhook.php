<?php

declare(strict_types=1);

namespace BillingServ;

/**
 * Verifies and parses outbound BillingServ webhook deliveries.
 *
 * When a Webhook Signing Secret is configured, the BillingServ-Signature header
 * carries `t=<unix_timestamp>,v1=<hex_signature>` where the signature is an
 * HMAC-SHA-256 over `timestamp + "." + raw request body`.
 */
final class Webhook
{
    /** Verify the raw (unparsed) payload against its BillingServ-Signature header. */
    public static function verify(string $payload, string $signatureHeader, string $secret, int $tolerance = 300): bool
    {
        $timestamp = null;
        $signatures = [];

        foreach (explode(',', $signatureHeader) as $part) {
            if (!str_contains($part, '=')) {
                continue;
            }
            [$key, $value] = explode('=', trim($part), 2);
            if ($key === 't' && ctype_digit($value)) {
                $timestamp = (int) $value;
            } elseif ($key === 'v1') {
                $signatures[] = $value;
            }
        }

        if ($timestamp === null || $signatures === []) {
            return false;
        }

        if ($tolerance > 0 && abs(time() - $timestamp) > $tolerance) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

        foreach ($signatures as $signature) {
            if (hash_equals($expected, strtolower($signature))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Decode a verified webhook envelope: {id, type, version, created_at, data}.
     *
     * @return array<string, mixed>
     */
    public static function parse(string $payload): array
    {
        try {
            $envelope = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \InvalidArgumentException('Webhook payload is not valid JSON.', 0, $e);
        }

        return is_array($envelope) ? $envelope : throw new \InvalidArgumentException('Webhook payload must be a JSON object.');
    }
}
