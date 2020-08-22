<?php

declare(strict_types=1);

namespace Chinstrap\Core\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Chinstrap\Core\Generators\XmlStringSitemap;

final class SitemapGeneratorProvider extends AbstractServiceProvider
{
    protected $provides = ['Chinstrap\Core\Contracts\Generators\Sitemap'];

    public function register(): void
    {
        // Register items
        $container = $this->getContainer();
        $container->add('Chinstrap\Core\Contracts\Generators\Sitemap', function () use ($container) {
            $config = $container->get('PublishingKit\Config\Config');
            $source = $container->get('Chinstrap\Core\Contracts\Sources\Source');
            return new XmlStringSitemap($config, $source);
        });
    }
}
