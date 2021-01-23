<?php

declare(strict_types=1);

namespace Chinstrap\Core\Http\Controllers;

use League\Glide\Server;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ImageController
{
    /**
     * @var Server
     */
    private $glide;

    public function __construct(Server $glide)
    {
        $this->glide = $glide;
    }

    public function get(ServerRequestInterface $request, array $args): ResponseInterface
    {
        return $this->glide->getImageResponse($args['name'], $request->getQueryParams());
    }
}
