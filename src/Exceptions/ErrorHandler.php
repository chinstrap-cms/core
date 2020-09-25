<?php

declare(strict_types=1);

namespace Chinstrap\Core\Exceptions;

use Laminas\Diactoros\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Throwable;

final class ErrorHandler
{
    /**
     * @var ResponseFactory
     */
    private $factory;

    public function __construct(ResponseFactory $factory)
    {
        $this->factory = $factory;
    }

    public function __invoke(Throwable $e): ResponseInterface
    {
        $response = $this->factory->createResponse(500);
        $response->getBody()->write(sprintf(
            'An error occurred: %s',
            $e->getMessage
        ));
        return $response;
    }
}
