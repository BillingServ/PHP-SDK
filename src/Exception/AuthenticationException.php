<?php

declare(strict_types=1);

namespace BillingServ\Exception;

/** Thrown on HTTP 401: missing or invalid API key. */
final class AuthenticationException extends ApiException
{
}
