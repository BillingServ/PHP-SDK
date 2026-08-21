<?php

declare(strict_types=1);

namespace BillingServ\Resource;

use BillingServ\Client;

abstract class Resource
{
    public function __construct(protected readonly Client $client)
    {
    }
}
