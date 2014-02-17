<?php

namespace Yjv\Bundle\ValidatorConstraintGroupBundle\DependencyInjection;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class YjvValidatorConstraintGroupExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        foreach ($container->getParameter('kernel.bundles') as $name => $bundle) {

            $reflection = new \ReflectionClass($bundle);

            if (is_file($file = dirname($reflection->getFilename()).'/Resources/config/validator_constraint_groups.yml')) {

                $loaderId = sprintf('yjv.validator_constraint_group.group_factory.loader.%s', $name);
                $loader = new Definition();
                $loader
                    ->setClass($container->getParameter('yjv.validator_constraint_group.yaml_group_loader.class'))
                    ->setArguments(array(realpath($file)))
                ;
                $container->setDefinition($loaderId, $loader);
                $container->getDefinition('yjv.validator_constraint_group.group_factory')
                    ->addMethodCall('addLoader', array(new Reference($loaderId)))
                ;
                $container->addResource(new FileResource($file));
            }
        }
    }
}
