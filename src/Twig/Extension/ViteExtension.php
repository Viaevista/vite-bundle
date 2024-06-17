<?php

namespace Viaevista\ViteBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Viaevista\ViteBundle\Twig\Runtime\ViteExtensionRuntime;

class ViteExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('vite', [ViteExtensionRuntime::class, 'includeViteAssets'], ['is_safe' => ['html']]),
        ];
    }
}
