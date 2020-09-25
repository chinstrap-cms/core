<?php

declare(strict_types=1);

namespace Chinstrap\Core\Listeners;

use Chinstrap\Core\Contracts\Services\Navigator;
use Chinstrap\Core\Views\Filters\Mix;
use Chinstrap\Core\Views\Filters\Version;
use Chinstrap\Core\Views\Functions\Form;
use League\Event\EventInterface;
use Twig\Environment;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class RegisterViews extends BaseListener
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var Version
     */
    private $version;

    /**
     * @var Mix
     */
    private $mix;

    /**
     * @var Form
     */
    private $form;

    /**
     * @var Navigator
     */
    private $navigator;

    public function __construct(Environment $twig, Version $version, Mix $mix, Form $form, Navigator $navigator)
    {
        $this->twig = $twig;
        $this->version = $version;
        $this->mix = $mix;
        $this->form = $form;
        $this->navigator = $navigator;
    }

    public function handle(EventInterface $event)
    {
        $this->twig->addFilter(new TwigFilter('version', $this->version));
        $this->twig->addFilter(new TwigFilter('mix', $this->mix));
        $this->twig->addFunction(new TwigFunction(
            'form',
            $this->form
        ));
        $this->twig->addGlobal('navigation', $this->navigator->__invoke());
    }
}
