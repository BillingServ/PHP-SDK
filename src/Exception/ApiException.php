<?php

declare(strict_types=1);

namespace BillingServ\Exception;

/**
 * An API request failed with an HTTP error status.
 *
 * getErrors() holds the raw `errors` payload (string or field => messages array),
 * getErrorCode() the machine-readable code (e.g. "country_not_found") and
 * getRequestId() the X-Request-ID correlation value when provided by the API.
 */
class ApiException extends BillingServException
{
    public function __construct(
        string $message,
        private readonly int $status,
        private readonly string|array|null $errors = null,
        private readonly ?string $errorCode = null,
        private readonly ?string $requestId = null,
        private readonly ?array $response = null,
    ) {
        parent::__construct($message);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    /** @return string|array<string, mixed>|null */
    public function getErrors(): string|array|null
    {
        return $this->errors;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getRequestId(): ?string
    {
        return $this->requestId;
    }

    /** The decoded JSON error response body. */
    public function getResponse(): ?array
    {
        return $this->response;
    }
}
