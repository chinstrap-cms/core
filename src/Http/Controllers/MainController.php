<?php

declare(strict_types=1);

namespace Chinstrap\Core\Http\Controllers;

use Chinstrap\Core\Contracts\Sources\Source;
use Chinstrap\Core\Contracts\Views\Renderer;
use Chinstrap\Core\Events\FormSubmitted;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\EventManager\EventManagerInterface;
use League\Route\Http\Exception\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class MainController
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

    public function __construct(ResponseInterface $response, Source $source, Renderer $view, EventManagerInterface $eventManager)
    {
        $this->response = $response;
        $this->source = $source;
        $this->view = $view;
        $this->eventManager = $eventManager;
    }

    public function index(ServerRequestInterface $request, array $args): ResponseInterface
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

    public function submit(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $name = isset($args['name']) ? $args['name'] : 'index';
        if (!$document = $this->source->find($name)) {
            throw new NotFoundException('Page not found');
        }
        $data = $document->getFields();
        if (!isset($data['forms'])) {
            return new EmptyResponse(405);
        }
        $data['content'] = $document->getContent();
        $layout = isset($data['layout']) ? $data['layout'] . '.html' : 'default.html';
        $event = new FormSubmitted();
        $this->eventManager->triggerEvent($event);
        return $this->view->render($this->response, $layout, $data);
    }
}
