<?php

namespace Chinstrap\Core\Contracts\Services;

use Chinstrap\Core\Objects\Navigation\Container;

interface Navigator
{
    public function __invoke(): Container;
}
