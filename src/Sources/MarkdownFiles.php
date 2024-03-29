<?php

declare(strict_types=1);

namespace Chinstrap\Core\Sources;

use Chinstrap\Core\Contracts\Objects\Document;
use Chinstrap\Core\Contracts\Sources\Source;
use Chinstrap\Core\Objects\MarkdownDocument;
use DateTime;
use League\Flysystem\FilesystemInterface;
use Mni\FrontYAML\Document as ParsedDocument;
use Mni\FrontYAML\Parser;
use PublishingKit\Utilities\Collections\LazyCollection;
use PublishingKit\Utilities\Contracts\Collectable;

final class MarkdownFiles implements Source
{
    protected FilesystemInterface $fs;

    protected Parser $parser;

    public function __construct(FilesystemInterface $fs, Parser $parser)
    {
        $this->fs = $fs;
        $this->parser = $parser;
    }

    public function all(): Collectable
    {
        return LazyCollection::make(function () {
            /** @var array<array{type: string, path: string}> $files **/
            $files = $this->fs->listContents('content://', true);
            foreach ($files as $file) {
                if ($file['type'] === 'dir') {
                    continue;
                }
                if (!preg_match('/.(markdown|md)$/', $file['path'])) {
                    continue;
                }
                if (!$content = $this->fs->read('content://' . $file['path'])) {
                    continue;
                }

                yield $this->fromMarkdown($this->parser->parse($content), $file['path']);
            }
        });
    }

    public function find(string $name): ?Document
    {
        // Does that page exist?
        $path = rtrim($name, '/') . '.md';
        if (!$this->fs->has("content://" . $path)) {
            return null;
        }

        // Get content
        if (!$rawcontent = $this->fs->read("content://" . $path)) {
            return null;
        }
        return $this->fromMarkdown($this->parser->parse($rawcontent), $path);
    }

    private function fromMarkdown(ParsedDocument $doc, string $path): MarkdownDocument
    {
        $document = new MarkdownDocument();
        $document->setContent($doc->getContent());
        /** @var string $field **/
        /** @var string|array $value **/
        foreach ($doc->getYAML() as $field => $value) {
            assert(is_string($field));
            $document->setField($field, $value);
        }
        $document->setPath($path);
        $lastUpdated = new DateTime();
        if ($timestamp = $this->fs->getTimestamp("content://" . $path)) {
            $lastUpdated->setTimestamp($timestamp);
        }
        $document->setUpdatedAt($lastUpdated);
        return $document;
    }
}
