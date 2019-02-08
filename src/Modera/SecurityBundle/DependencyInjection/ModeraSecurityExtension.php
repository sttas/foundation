<?php

namespace Modera\SecurityBundle\DependencyInjection;

use Modera\BackendSecurityBundle\DependencyInjection\ModeraBackendSecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ModeraSecurityExtension extends Extension implements PrependExtensionInterface
{
    const CONFIG_KEY = 'modera_security.config';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $this->injectConfigIntoContainer($config, $container);

        $container
            ->setAlias('modera_security.root_user_handling.handler', $config['root_user_handler'])
            ->setPublic(true)
        ;

        if (class_exists('Symfony\Component\Console\Application')) {
            try {
                $loader->load('console.xml');
            } catch (\Exception $e) {}
        }
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function injectConfigIntoContainer(array $config, ContainerBuilder $container)
    {
        $container->setParameter(self::CONFIG_KEY, $config);

        $container->setParameter(
            'modera_security.password_strength.mail.sender',
            $config['password_strength']['mail']['sender']
        );


    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig($this->getAlias());

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(self::CONFIG_KEY.'.switch_user', $config['switch_user']);
        $container->setParameter(self::CONFIG_KEY.'.access_control', $config['access_control']);
    }
}
