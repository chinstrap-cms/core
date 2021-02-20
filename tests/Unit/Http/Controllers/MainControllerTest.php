<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Http\Controllers;

use Chinstrap\Core\Http\Controllers\MainController;
use Chinstrap\Core\Objects\MarkdownDocument;
use Chinstrap\Core\Tests\TestCase;
use DateTime;
use Laminas\EventManager\EventManagerInterface;
use Mockery as m;

final class MainControllerTest extends TestCase
{
    public function testGetResponse(): void
    {
        $emitter = m::mock(EventManagerInterface::class);
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $timestamp = (new DateTime())->setTimestamp(1568840820);
        $response->shouldReceive('withAddedHeader')
            ->with('Last-Modified', $timestamp->format('D, d M Y H:i:s') . ' GMT')
            ->once()
            ->andReturn($response);
        $doc = (new MarkdownDocument())
            ->setField('title', 'Foo')
            ->setPath('foo.md')
            ->setContent('foo')
            ->setUpdatedAt($timestamp);
        $source = m::mock('Chinstrap\Core\Contracts\Sources\Source');
        $source->shouldReceive('find')->once()->andReturn($doc);
        $view = m::mock('Chinstrap\Core\Contracts\Views\Renderer');
        $view->shouldReceive('render')->with(
            $response,
            'default.html',
            [
             'title' => 'Foo',
             'content' => 'foo',
            ]
        )->once();
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $controller = new MainController(
            $response,
            $source,
            $view,
            $emitter
        );
        $controller->index($request, ['name' => 'foo']);
    }

    public function testPostResponse(): void
    {
        $emitter = m::mock(EventManagerInterface::class);
        $emitter->shouldReceive('trigger')->with('Chinstrap\Core\Events\FormSubmitted')->once();
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $doc = (new MarkdownDocument())
            ->setField('title', 'Foo')
            ->setField('forms', ['contact'])
            ->setPath('foo.md')
            ->setContent('foo');
        $source = m::mock('Chinstrap\Core\Contracts\Sources\Source');
        $source->shouldReceive('find')->once()->andReturn($doc);
        $view = m::mock('Chinstrap\Core\Contracts\Views\Renderer');
        $view->shouldReceive('render')->with(
            $response,
            'default.html',
            [
             'title' => 'Foo',
             'content' => 'foo',
             'forms' => ['contact'],
            ]
        )->once();
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $controller = new MainController(
            $response,
            $source,
            $view,
            $emitter
        );
        $controller->submit($request, ['name' => 'foo']);
    }

    public function testPostResponseToUnregisteredForm(): void
    {
        $emitter = m::mock(EventManagerInterface::class);
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $doc = (new MarkdownDocument())
            ->setField('title', 'Foo')
            ->setPath('foo.md')
            ->setContent('foo');
        $source = m::mock('Chinstrap\Core\Contracts\Sources\Source');
        $source->shouldReceive('find')->once()->andReturn($doc);
        $view = m::mock('Chinstrap\Core\Contracts\Views\Renderer');
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $controller = new MainController(
            $response,
            $source,
            $view,
            $emitter
        );
        $response = $controller->submit($request, ['name' => 'foo']);
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function test404(): void
    {
        $this->expectException('League\Route\Http\Exception\NotFoundException');
        $emitter = m::mock(EventManagerInterface::class);
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $source = m::mock('Chinstrap\Core\Contracts\Sources\Source');
        $source->shouldReceive('find')->once()->andReturn(null);
        $view = m::mock('Chinstrap\Core\Contracts\Views\Renderer');
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $controller = new MainController(
            $response,
            $source,
            $view,
            $emitter
        );
        $controller->index($request, ['name' => 'foo']);
    }

    public function test404Submit(): void
    {
        $this->expectException('League\Route\Http\Exception\NotFoundException');
        $emitter = m::mock(EventManagerInterface::class);
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $source = m::mock('Chinstrap\Core\Contracts\Sources\Source');
        $source->shouldReceive('find')->once()->andReturn(null);
        $view = m::mock('Chinstrap\Core\Contracts\Views\Renderer');
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');
        $controller = new MainController(
            $response,
            $source,
            $view,
            $emitter
        );
        $controller->submit($request, ['name' => 'foo']);
    }
}
