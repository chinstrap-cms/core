<?php

declare(strict_types=1);

namespace Chinstrap\Core\Views\Filters;

use Exception;

final class Mix
{
    public function __invoke(string $path): string
    {
        if (!defined('PUBLIC_DIR')) {
            throw new Exception('Public dir not defined');
        }
        /** @var array $manifest **/
        $manifest = json_decode(rtrim(file_get_contents(PUBLIC_DIR . DIRECTORY_SEPARATOR . 'mix-manifest.json')), true);
        if (! array_key_exists("/" . $path, $manifest)) {
            throw new Exception(
                "Unable to locate Mix file: {$path}"
            );
        }
        if (!file_exists(PUBLIC_DIR . DIRECTORY_SEPARATOR . $path)) {
            throw new Exception('Included file does not exist');
        }

        if (file_exists(PUBLIC_DIR . DIRECTORY_SEPARATOR . '/hot')) {
            $url = rtrim(file_get_contents(PUBLIC_DIR . DIRECTORY_SEPARATOR . '/hot'));

            if ($this->startsWith($url, 'http://') || $this->startsWith($url, 'https://')) {
                return $this->after($url, ':') . $path;
            }

            return "//localhost:8080{$path}";
        }
        return (string)$manifest["/" . $path];
    }

    private function startsWith(string $text, string $start): bool
    {
          $len = strlen($start);
          return (substr($text, 0, $len) === $start);
    }

    private function after(string $text, string $separator): string
    {
        return substr($text, strpos($text, $separator) + 1);
    }
}
