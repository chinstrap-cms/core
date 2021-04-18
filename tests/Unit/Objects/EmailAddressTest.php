<?php

declare(strict_types=1);

namespace Chinstrap\Core\Tests\Unit\Objects;

use Chinstrap\Core\Objects\EmailAddress;
use Chinstrap\Core\Tests\TestCase;

final class EmailAddressTest extends TestCase
{
    public function testCreate(): void
    {
        $email = "bob@example.com";
        $obj = new EmailAddress($email);
        $this->assertEquals($email, $obj->__toString());
    }

    public function testFailedCreate(): void
    {
        $this->expectException('Chinstrap\Core\Exceptions\Objects\EmailAddressInvalid');
        $email = "example.com";
        $obj = new EmailAddress($email);
    }

    public function testEquals(): void
    {
        $obj = new EmailAddress('bob@example.com');
        $this->assertTrue($obj->equals(new EmailAddress('bob@example.com')));
        $this->assertFalse($obj->equals(new EmailAddress('eric@example.com')));
    }
}
