<?php

declare(strict_types=1);

namespace BillingServ\Exception;

/** Thrown on HTTP 429: rate limit exceeded. */
final class RateLimitException extends ApiException
{
    public function __construct(
        string $message,
        int $status,
        string|array|null $errors = null,
        ?string $errorCode = null,
        ?string $requestId = null,
        ?array $response = null,
        private readonly ?int $retryAfter = null,
    ) {
        parent::__construct($message, $status, $errors, $errorCode, $requestId, $response);
    }

    /** Seconds to wait before retrying, when the API advertises it. */
    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }
}
