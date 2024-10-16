<?php

declare(strict_types=1);

namespace Paysera\DeliverySdk\Service;

use Exception;

interface DeliveryLoggerInterface
{
    public function info(string $message): void;

    public function error(string $message, Exception $exception = null): void;
}
