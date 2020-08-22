<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Views;

use Chinstrap\Core\Tests\TestCase;
use Mockery as m;
use Chinstrap\Core\Views\TwigRenderer;

final class TwigRendererTest extends TestCase
{
    public function testRenderer(): void
    {
        $this->markTestIncomplete();
        $twig = m::mock('Twig\Environment');
        $twig->shouldReceive('load')->with('foo.html')->once()->andReturn($twig);
        $twig->shouldReceive('render')->with(['Foo'])->once()->andReturn(['Foo']);
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->once()->andReturn($response);
        $response->shouldReceive('write')->once()->andReturn($response);
        $renderer = new TwigRenderer($twig);
        $renderer->render($response, 'foo.html', ['Foo']);
    }
}
