<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Console\Commands;

use Chinstrap\Core\Tests\TestCase;
use Mockery as m;
use Symfony\Component\Console\Tester\CommandTester;
use Chinstrap\Core\Console\Commands\Server;
use phpmock\mockery\PHPMockery;

final class ServerTest extends TestCase
{
    public function testExecute()
    {
        $passthru = PHPMockery::mock('Chinstrap\Core\Console\Commands', "passthru");
        $cmd = new Server();
        $tester = new CommandTester($cmd);
        $tester->execute([]);
        $this->assertEquals('server', $cmd->getName());
        $this->assertEquals('Runs the development server', $cmd->getDescription());
        $this->assertEquals('This command runs the development server', $cmd->getHelp());
    }
}
