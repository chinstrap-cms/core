<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Views;

use Chinstrap\Core\Tests\TestCase;
use Chinstrap\Core\Views\TwigRenderer;
use Mockery as m;
use Twig\TemplateWrapper;

final class TwigRendererTest extends TestCase
{
    public function testRenderer(): void
    {
        $twig = m::mock('Twig\Environment');
        $tmpl = m::mock('Twig\Template');
        $tmpl->shouldReceive('render')->once()->andReturn('Foo');
        $wrapper = new TemplateWrapper($twig, $tmpl);
        $twig->shouldReceive('load')->with('foo.html')->once()->andReturn($wrapper);
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->once()->andReturn($response);
        $response->shouldReceive('write')->once()->andReturn($response);
        $renderer = new TwigRenderer($twig);
        $renderer->render($response, 'foo.html', ['Foo']);
    }
}
