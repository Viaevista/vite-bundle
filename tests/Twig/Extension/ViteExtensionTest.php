<?php

namespace Viaevista\ViteBundle\Tests\Twig\Extension;

use Viaevista\ViteBundle\Tests\UnitTestCase;
use Viaevista\ViteBundle\Twig\Extension\ViteExtension;
use Viaevista\ViteBundle\Twig\Runtime\ViteExtensionRuntime;

class ViteExtensionTest extends UnitTestCase
{
    public function testViteFunctionIsExported(): void
    {
        // Prepare
        $extension = new ViteExtension();

        // Execute
        [$function] = $extension->getFunctions();

        // Assert
        $this->assertSame('vite', $function->getName());
        $this->assertSame([ViteExtensionRuntime::class, 'includeViteAssets'], $function->getCallable());
    }

}
