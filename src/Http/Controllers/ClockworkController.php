<?php

declare(strict_types=1);

namespace Chinstrap\Core\Http\Controllers;

use Clockwork\Support\Vanilla\Clockwork;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

final class ClockworkController
{
    /**
     * @var Clockwork
     */
    private $clockwork;

    public function __construct(Clockwork $clockwork)
    {
        $this->clockwork = $clockwork;
    }

    public function process(ServerRequestInterface $request, array $requestName): JsonResponse
    {
        return new JsonResponse($this->clockwork->getMetadata($requestName['request']));
    }
}
