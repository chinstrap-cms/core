<?php

declare(strict_types=1);

namespace Chinstrap\Core\Kernel;

use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Laminas\ServiceManager\ServiceManager;

final class ContainerFactory
{
    public function __invoke(): ServiceManager
    {
        return new ServiceManager([
            'abstract_factories' => ReflectionBasedAbstractFactory::class,
        ]);
    }
}
