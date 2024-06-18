<?php

namespace Viaevista\ViteBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class ViaevistaViteBundle extends AbstractBundle
{
    private const VITE_SERVER_HOST_DEFAULT = 'http://localhost:5173';
    private const VITE_SERVER_HOST_USE_SERVER_MODE_DEFAULT = false;
    private const VITE_BASE_PATH_DEFAULT = '';


    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
            ->arrayNode('server') // Vite server config
            ->children()
            ->booleanNode('use_server_mode')->end()
            ->scalarNode('host')->end()
            ->end()
            ->end() // END Vite server config
            ->scalarNode('base_path')->end()
            ->end()
        ;
    }

    /**
     * @param array{server:array{use_server_mode: bool|null, host: string|null}, base_path: string|null} $config
     * @param ContainerConfigurator $container
     * @param ContainerBuilder $builder
     * @return void
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');

        $useServerMode = $config['server']['use_server_mode'] ?? self::VITE_SERVER_HOST_DEFAULT;
        $viteServerHost = $config['server']['host'] ?? self::VITE_SERVER_HOST_USE_SERVER_MODE_DEFAULT;
        $viteBasePath = $config['base_path'] ?? self::VITE_BASE_PATH_DEFAULT;

        $container->parameters()->set('viaevista_vite.server.use_server_mode', $useServerMode);
        $container->parameters()->set('viaevista_vite.server.host', $viteServerHost);
        $container->parameters()->set('viaevista_vite.base_path', $viteBasePath);
    }
}
