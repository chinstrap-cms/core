<?php

declare(strict_types=1);

namespace Chinstrap\Core\Http\Handlers;

use Chinstrap\Core\Contracts\Sources\Source;
use Chinstrap\Core\Contracts\Views\Renderer;
use Laminas\EventManager\EventManagerInterface;
use League\Route\Http\Exception\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class PageHandler
{
    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var Source
     */
    protected $source;

    /**
     * @var Renderer
     */
    protected $view;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    public function __construct(
        ResponseInterface $response,
        Source $source,
        Renderer $view,
        EventManagerInterface $eventManager
    ) {
        $this->response = $response;
        $this->source = $source;
        $this->view = $view;
        $this->eventManager = $eventManager;
    }

    /**
     * GET request to content page
     *
     * @param ServerRequestInterface $request
     * @param array{name: ?string} $args
     */
    public function __invoke(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $name = isset($args['name']) ? $args['name'] : 'index';
        if (!$document = $this->source->find($name)) {
            throw new NotFoundException('Page not found');
        }
        $data = $document->getFields();
        $data['content'] = $document->getContent();
        $layout = isset($data['layout']) ? $data['layout'] . '.html' : 'default.html';
        $response = $this->response->withAddedHeader(
            'Last-Modified',
            $document->getUpdatedAt()->format('D, d M Y H:i:s') . ' GMT'
        );
        return $this->view->render($response, $layout, $data);
    }
}
