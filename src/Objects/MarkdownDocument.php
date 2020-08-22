<?php

declare(strict_types=1);

namespace Chinstrap\Core\Objects;

use Chinstrap\Core\Contracts\Objects\Document;
use JsonSerializable;
use DateTime;

final class MarkdownDocument implements Document, JsonSerializable
{
    /**
     * @var string
     */
    protected $content;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $path;

    /**
     * @var DateTime
     */
    private $updatedAt;

    public function __construct()
    {
        $this->content = '';
        $this->data = [];
        $this->path = '';
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $data): Document
    {
        $this->content = $data;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getField(string $key)
    {
        if (!isset($this->data[$key])) {
            return null;
        }
        return $this->data[$key];
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setField(string $key, $value): Document
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUrl(): string
    {
        return '/' . preg_replace('/index$/', '', $this->stripExtension($this->getPath()));
    }

    public function setPath(string $path): Document
    {
        $this->path = $path;
        return $this;
    }

    public function __toString(): string
    {
        return $this->content;
    }

    public function __get(string $name): string
    {
        if ($name == 'content') {
            return $this->getContent();
        }
        if ($name == 'path') {
            return $this->getPath();
        }
        return $this->getField($name);
    }

    public function __set(string $name, string $value): Document
    {
        if ($name == 'content') {
            $this->setContent($value);
        } else if ($name == 'path') {
            $this->setPath($value);
        } else {
            $this->setField($name, $value);
        }
        return $this;
    }

    public function getFields(): array
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        $data = $this->getFields();
        $data['content'] = $this->getContent();
        $data['url'] = $this->getUrl();
        return $data;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): Document
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    private function stripExtension(string $path): string
    {
        return preg_replace('/.(markdown|md)$/', '', $path);
    }
}
