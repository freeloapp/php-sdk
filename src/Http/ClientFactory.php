<?php

declare(strict_types=1);

namespace Freelo\Sdk\Http;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Factory for creating HTTP clients using PSR discovery
 */
class ClientFactory
{
    /**
     * Create HTTP client using discovery
     */
    public static function createClient(): ClientInterface
    {
        return Psr18ClientDiscovery::find();
    }

    /**
     * Create request factory using discovery
     */
    public static function createRequestFactory(): RequestFactoryInterface
    {
        return Psr17FactoryDiscovery::findRequestFactory();
    }

    /**
     * Create stream factory using discovery
     */
    public static function createStreamFactory(): StreamFactoryInterface
    {
        return Psr17FactoryDiscovery::findStreamFactory();
    }
}
