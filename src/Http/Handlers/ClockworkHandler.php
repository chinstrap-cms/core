<?php

declare(strict_types=1);

namespace Chinstrap\Core\Http\Handlers;

use Clockwork\Support\Vanilla\Clockwork;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

final class ClockworkHandler
{
    /**
     * @var Clockwork
     */
    private $clockwork;

    public function __construct(Clockwork $clockwork)
    {
        $this->clockwork = $clockwork;
    }

    public function __invoke(ServerRequestInterface $request, array $requestName): JsonResponse
    {
        return new JsonResponse($this->clockwork->getMetadata($requestName['request']));
    }
}
