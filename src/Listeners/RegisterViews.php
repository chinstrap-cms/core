<?php

declare(strict_types=1);

namespace Chinstrap\Core\Listeners;

use Chinstrap\Core\Views\Filters\Mix;
use Chinstrap\Core\Views\Filters\Version;
use Chinstrap\Core\Views\Functions\Form;
use Laminas\EventManager\EventInterface;
use Twig\Environment;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class RegisterViews extends BaseListener
{
    private Environment $twig;

    private Version $version;

    private Mix $mix;

    private Form $form;

    public function __construct(Environment $twig, Version $version, Mix $mix, Form $form)
    {
        $this->twig = $twig;
        $this->version = $version;
        $this->mix = $mix;
        $this->form = $form;
    }

    public function __invoke(EventInterface $event): void
    {
        $this->twig->addFilter(new TwigFilter('version', $this->version));
        $this->twig->addFilter(new TwigFilter('mix', $this->mix));
        $this->twig->addFunction(new TwigFunction(
            'form',
            $this->form
        ));
    }
}
