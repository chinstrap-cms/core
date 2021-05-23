<?php

declare(strict_types=1);

namespace Chinstrap\Core\Http\Handlers;

use League\Glide\Server;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ImageHandler
{
    private Server $glide;

    public function __construct(Server $glide)
    {
        $this->glide = $glide;
    }

    /**
     * GET request to content page
     *
     * @param ServerRequestInterface $request
     * @param array{name: string} $args
     */
    public function __invoke(ServerRequestInterface $request, array $args): ResponseInterface
    {
        return $this->glide->getImageResponse($args['name'], $request->getQueryParams());
    }
}
