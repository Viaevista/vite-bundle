<?php

namespace  Viaevista\ViteBundle\Twig\Runtime;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Extension\RuntimeExtensionInterface;

readonly class ViteExtensionRuntime implements RuntimeExtensionInterface
{
    private string $viteServeurHost;
    private string $basePath;

    public function __construct(
        #[Autowire('%kernel.project_dir%/public')]
        private string $publicDir,
        #[Autowire('%viaevista_vite.server.use_server_mode%')]
        private bool $useViteServerMode,
        #[Autowire('%viaevista_vite.server.host%')]
        string $viteServeurHost,
        #[Autowire('%viaevista_vite.base_path%')]
        string $basePath,
        private Filesystem $filesystem,
    ) {
        $this->viteServeurHost = rtrim($viteServeurHost, '/');

        $addStartSlashToBasePath = str_starts_with($basePath, '/') ? $basePath : "/$basePath";
        $addEndSlashToBasePath = str_ends_with($addStartSlashToBasePath, '/') ? $addStartSlashToBasePath : "$addStartSlashToBasePath/";
        $this->basePath = $addEndSlashToBasePath;
    }

    public function includeViteAssets(string $entrypoint): string
    {
        return $this->useViteServerMode ? $this->generateHtmlForServerMode($entrypoint) : $this->generateHtmlForStaticAssets($entrypoint);
    }

    private function generateAssetUrl(string $path): string
    {
        // TODO add a better way to parse path
        $normalizedPath = ltrim($path, '/');
        return  $this->basePath . $normalizedPath;
    }

    private function generateHtmlForServerMode(string $entrypoint): string
    {
        $htmlElements = [
            '<script type="module" src="' . $this->viteServeurHost . $this->generateAssetUrl('/@vite/client') . '"></script>',
            '<script type="module" src="' . $this->viteServeurHost . $this->generateAssetUrl($entrypoint) . '"></script>',
        ];

        return implode(PHP_EOL, $htmlElements);
    }

    private function generateHtmlForStaticAssets(string $entrypoint): string
    {
        $manifest = $this->parseManifest($entrypoint);
        $file = $manifest['file'] ?? '';
        $css = $manifest['css'] ?? '';
        $imports = $manifest['imports'] ?? [];

        $htmlElements = ['<script type="module" src="' . $this->generateAssetUrl($file) . '"></script>'];

        foreach ($css as $cssFile) {
            $htmlElements[] = '<link rel="stylesheet" href="' . $this->generateAssetUrl($cssFile) . '"/>';
        }

        foreach ($imports as $import) {
            $htmlElements[] = '<link rel="modulepreload" href="' . $this->generateAssetUrl($import) . '"/>';
        }

        return implode(PHP_EOL, $htmlElements);
    }

    private function parseManifest(string $entrypoint): array
    {
        $manifestPath = $this->publicDir . $this->generateAssetUrl('.vite/manifest.json');
        $manifestContent = $this->filesystem->readFile($manifestPath);
        $manifestParsed = json_decode($manifestContent, true);
        return is_array($manifestParsed) ? ($manifestParsed[$entrypoint] ?? []) : [];
    }
}
