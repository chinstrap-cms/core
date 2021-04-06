<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Views\Filters;

use Chinstrap\Core\Tests\TestCase;
use Chinstrap\Core\Views\Filters\Version;

final class VersionTest extends TestCase
{
    public function testRun(): void
    {
        $version = new Version();
        $result = $version('index.php');
        $this->assertEquals(1, preg_match('/^\/index\.php\?v=\d+$/', $result));
    }
}
