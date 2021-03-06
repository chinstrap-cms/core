<?php

declare(strict_types=1);

namespace Chinstrap\Core\Objects;

use Chinstrap\Core\Exceptions\Objects\EmailAddressInvalid;

final class EmailAddress
{
    private string $email;

    public function __construct(string $email)
    {
        if (!(filter_var($email, FILTER_VALIDATE_EMAIL))) {
            throw new EmailAddressInvalid('The provided email address is not valid');
        }
        $this->email = $email;
    }

    public function equals(EmailAddress $email): bool
    {
        return ($this->email === $email->__toString());
    }

    public function __toString(): string
    {
        return $this->email;
    }
}
