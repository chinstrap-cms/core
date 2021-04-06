<?php

declare(strict_types=1);

namespace Chinstrap\Core\Views\Filters;

final class Version
{
    public function __invoke(string $path): string
    {
        if (!defined('PUBLIC_DIR')) {
            throw new \Exception('Public dir not defined');
        }
        return DIRECTORY_SEPARATOR . $path . "?v=" . filemtime(PUBLIC_DIR . DIRECTORY_SEPARATOR . $path);
    }
}
