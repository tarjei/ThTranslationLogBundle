<?php

namespace TH\TranslationLogBundle\DependencyInjection;
/**
 * @author Tarjei Huse <tarjei@scanmine.com>
 */
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;


/**
 * Injects Handlers into the NotificationManager
 *
 */
class TranslationLogCompilerPass implements CompilerPassInterface
{
    /**
     * Process the config
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $translator = $container->findDefinition('translator.default');
        $translator->setClass('TH\TranslationLogBundle\Translator');

    }
}