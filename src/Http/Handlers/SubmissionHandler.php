<?php

declare(strict_types=1);

namespace Chinstrap\Core\Http\Handlers;

use Chinstrap\Core\Contracts\Sources\Source;
use Chinstrap\Core\Contracts\Views\Renderer;
use Chinstrap\Core\Events\FormSubmitted;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\EventManager\EventManagerInterface;
use League\Route\Http\Exception\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class SubmissionHandler
{
    private ResponseInterface $response;
    private Source $source;
    private Renderer $view;
    private EventManagerInterface $eventManager;

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
     * POST request to content page
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
        if (!isset($data['forms'])) {
            return new EmptyResponse(405);
        }
        $data['content'] = $document->getContent();
        $layout = isset($data['layout']) ? $data['layout'] . '.html' : 'default.html';
        $this->eventManager->trigger(FormSubmitted::class);
        return $this->view->render($this->response, $layout, $data);
    }
}
