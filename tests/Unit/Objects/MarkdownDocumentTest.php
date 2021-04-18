<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Objects;

use Chinstrap\Core\Objects\MarkdownDocument;
use Chinstrap\Core\Tests\TestCase;

final class MarkdownDocumentTest extends TestCase
{
    public function testCreate(): void
    {
        $doc = new MarkdownDocument();
        $doc->setContent('This is my content');
        $doc->setField('title', 'My Page');
        $doc->setField('layout', 'custom.html');
        $doc->setPath('foo');
        $this->assertEquals('This is my content', $doc->getContent());
        $this->assertEquals('My Page', $doc->getField('title'));
        $this->assertEquals('custom.html', $doc->getField('layout'));
        $this->assertEquals('foo', $doc->getPath());
    }

    public function testTostring(): void
    {
        $doc = new MarkdownDocument();
        $doc->setContent('This is my content');
        $this->assertEquals('This is my content', $doc->__toString());
    }

    public function testSet(): void
    {
        $doc = new MarkdownDocument();
        $doc->content = 'This is my content';
        $doc->title = 'My Page';
        $doc->layout = 'custom.html';
        $doc->path = 'foo';
        $this->assertEquals('This is my content', $doc->getContent());
        $this->assertEquals('My Page', $doc->getField('title'));
        $this->assertEquals('custom.html', $doc->getField('layout'));
        $this->assertEquals('foo', $doc->getPath());
    }

    public function testGet(): void
    {
        $doc = new MarkdownDocument();
        $doc->setContent('This is my content');
        $doc->setField('title', 'My Page');
        $doc->setField('layout', 'custom.html');
        $doc->setPath('foo');
        $this->assertEquals('This is my content', $doc->content);
        $this->assertEquals('My Page', $doc->title);
        $this->assertEquals('custom.html', $doc->layout);
        $this->assertEquals('foo', $doc->path);
    }

    public function testGetFields(): void
    {
        $doc = new MarkdownDocument();
        $doc->setContent('This is my content');
        $doc->setField('title', 'My Page');
        $doc->setField('layout', 'custom.html');
        $doc->setPath('foo');
        $this->assertEquals([
                             'title' => 'My Page',
                             'layout' => 'custom.html',
                            ], $doc->getFields());
    }

    public function testJsonSerialize(): void
    {
        $doc = new MarkdownDocument();
        $doc->setContent('This is my content');
        $doc->setField('title', 'My Page');
        $doc->setField('layout', 'custom.html');
        $doc->setPath('foo');
        $this->assertEquals([
                             'title' => 'My Page',
                             'layout' => 'custom.html',
                             'content' => 'This is my content',
                             'url' => '/foo',
                            ], $doc->jsonSerialize());
    }
}
