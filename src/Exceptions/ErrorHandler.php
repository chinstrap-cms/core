<?php

declare(strict_types=1);

namespace Chinstrap\Core\Exceptions;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

final class ErrorHandler
{
    /**
     * Response factory instance
     *
     * @var ResponseFactoryInterface
     */
    private $factory;

    public function __construct(ResponseFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function __invoke(Throwable $e): ResponseInterface
    {
        $response = $this->factory->createResponse(500);
        $response->getBody()->write(sprintf(
            'An error occurred: %s',
            $e->getMessage()
        ));
        return $response;
    }
}
