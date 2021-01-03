<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Utilities;

use Chinstrap\Core\Tests\TestCase;
use Chinstrap\Core\Utilities\YamlWrapper;
use Mockery as m;

final class YamlWrapperTest extends TestCase
{
    public function testParse()
    {
        $originalContent = ['foo' => 'bar'];
        $parser = m::mock('Symfony\Component\Yaml\Yaml');
        $parser->shouldReceive('parse')->with('file')
            ->andReturn($originalContent);
        $wrapper = new YamlWrapper($parser);
        $this->assertEquals($originalContent, $wrapper->__invoke('file'));
    }
}
