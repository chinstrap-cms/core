<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Http\Handlers;

use Chinstrap\Core\Http\Handlers\SubmissionHandler;
use Chinstrap\Core\Objects\MarkdownDocument;
use Chinstrap\Core\Tests\TestCase;
use Laminas\EventManager\EventManagerInterface;
use Mockery as m;

final class SubmissionHandlerTest extends TestCase
{
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
        $handler = new SubmissionHandler(
            $response,
            $source,
            $view,
            $emitter
        );
        $handler($request, ['name' => 'foo']);
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
        $handler = new SubmissionHandler(
            $response,
            $source,
            $view,
            $emitter
        );
        $response = $handler($request, ['name' => 'foo']);
        $this->assertEquals(405, $response->getStatusCode());
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
        $handler = new SubmissionHandler(
            $response,
            $source,
            $view,
            $emitter
        );
        $handler($request, ['name' => 'foo']);
    }
}
