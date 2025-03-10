<?php

namespace Akeneo\Tool\Bundle\ElasticsearchBundle\DependencyInjection;

use Akeneo\Tool\Bundle\ElasticsearchBundle\Client;
use Akeneo\Tool\Bundle\ElasticsearchBundle\DualIndexationClient;
use Akeneo\Tool\Bundle\ElasticsearchBundle\IndexConfiguration\Loader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * @author    Julien Janvier <j.janvier@gmail.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/MIT MIT
 */
class AkeneoElasticsearchExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('commands.yml');
        $loader->load('services.yml');

        $this->registerEsClientsFromConfiguration($configs, $container);
    }

    /**
     * Dynamicaly instanciates Elasticsearch clients for each configuration made in parameter
     * `akeneo_elasticsearch.indexes`.
     *
     * Also registers those clients in the elasticsearch client registry `akeneo_elasticsearch.registry.clients`.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    private function registerEsClientsFromConfiguration(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $esClientRegistryDefinition = $container->getDefinition('akeneo_elasticsearch.registry.clients');

        foreach ($config['indexes'] as $index) {
            $configurationLoaderServiceName = sprintf(
                '%s.index_configuration.files',
                $index['service_name']
            );
            $container->register($configurationLoaderServiceName, Loader::class)
                ->setArguments([
                    $index['configuration_files'],
                    new Reference(ParameterBagInterface::class)
                ]);

            if (isset($index['activate_dual_indexation_with_service'])) {
                $container->register($index['service_name'], DualIndexationClient::class)
                    ->setArguments([
                        new Reference('akeneo_elasticsearch_native_client'),
                        new Reference($configurationLoaderServiceName),
                        $index['index_name'],
                        $index['id_prefix'],
                        $config['max_chunk_size'],
                        $config['max_expected_indexation_latency_in_milliseconds'],
                        $config['max_number_of_retries'],
                        new Reference($index['activate_dual_indexation_with_service']),
                    ])
                    ->setPublic(true);
            } else {
                $container->register($index['service_name'], Client::class)
                    ->setArguments([
                        new Reference('akeneo_elasticsearch_native_client'),
                        new Reference($configurationLoaderServiceName),
                        $index['index_name'],
                        $index['id_prefix'],
                        $config['max_chunk_size'],
                        $config['max_expected_indexation_latency_in_milliseconds'],
                        $config['max_number_of_retries']
                    ])
                    ->setPublic(true);
            }

            $esClientRegistryDefinition->addMethodCall('register', [new Reference($index['service_name'])]);
        }
    }
}
