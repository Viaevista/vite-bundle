<?php

namespace Viaevista\ViteBundle\Tests\Twig\Runtime;

use PHPUnit\Framework\Attributes\DataProvider;
use Viaevista\ViteBundle\Tests\UnitTestCase;
use Viaevista\ViteBundle\Twig\Runtime\ViteExtensionRuntime;

final class ViteExtensionRuntimeTest extends UnitTestCase
{
    public static function provideServerModeCases(): \Generator
    {
        yield ['http://localhost:5173', 'build', 'app.js'];
        yield ['http://localhost:5173', '/build', 'app.js'];
        yield ['http://localhost:5173', 'build/', 'app.js'];
        yield ['http://localhost:5173', '/build/', 'app.js'];
        yield ['http://localhost:5173/', 'build', 'app.js'];
        yield ['http://localhost:5173/', '/build', 'app.js'];
        yield ['http://localhost:5173/', 'build/', 'app.js'];
        yield ['http://localhost:5173/', '/build/', 'app.js'];
        yield ['http://localhost:5173/', '/build/', '/app.js'];
        yield ['http://localhost:5173/', '/build', '/app.js'];
    }

    #[DataProvider('provideServerModeCases')]
    public function testIncludeViteAssetsHtmlInServerMode(string $viteServerHost, string $viteBasePath): void
    {
        // Prepare
        $viteExtensionRuntime = $this->createViteExtensionRuntime(true, $viteServerHost, $viteBasePath);

        // Execute
        $html = $viteExtensionRuntime->includeViteAssets('app.js');

        // Assert - @see https://vitejs.dev/guide/backend-integration
        $expectedHtml = <<<HTML
<script type="module" src="http://localhost:5173/build/@vite/client"></script>
<script type="module" src="http://localhost:5173/build/app.js"></script>
HTML;

        $this->assertEquals($expectedHtml, $html);
    }

    #[DataProvider('provideServerModeCases')]
    public function testIncludeViteAssetsHtmlInBuiltMode(string $viteServerHost, string $viteBasePath): void
    {
        // Prepare
        $viteExtensionRuntime = $this->createViteExtensionRuntime(false, $viteServerHost, $viteBasePath);

        // Execute
        $html = $viteExtensionRuntime->includeViteAssets('app.js');

        // Assert - @see https://vitejs.dev/guide/backend-integration
        $expectedHtml = <<<HTML
<script type="module" src="/build/app.js-chunk-hash.js"></script>
<link rel="stylesheet" href="/build/app-file-chunk-hash.css"/>
<link rel="stylesheet" href="/build/app-other-file-chunk-hash.css"/>
<link rel="modulepreload" href="/build/imported-js-chunk-hash.js"/>
<link rel="modulepreload" href="/build/other-imported-js-chunk-hash.js"/>
HTML;

        $this->assertEquals($expectedHtml, $html);
    }

    private function createViteExtensionRuntime(bool $useServerMode, string $viteServerHost, string $basePath): ViteExtensionRuntime
    {
        return new ViteExtensionRuntime(
            __DIR__ . '/../../Fixtures/public',
            $useServerMode,
            $viteServerHost,
            $basePath
        );
    }
}
