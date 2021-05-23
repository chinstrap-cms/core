<?php

declare(strict_types=1);

namespace Chinstrap\Core\Views;

use Chinstrap\Core\Contracts\Views\Renderer;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment;

final class TwigRenderer implements Renderer
{
    protected Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render(ResponseInterface $response, string $template, array $data = []): ResponseInterface
    {
        $tpl = $this->twig->load($template);
        $response->getBody()->write($tpl->render($data));
        return $response;
    }
}
