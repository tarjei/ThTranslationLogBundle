<?php

namespace TH\TranslationLogBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use TH\TranslationLogBundle\DependencyInjection\TranslationLogCompilerPass;

class THTranslationLogBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new TranslationLogCompilerPass());
    }
}
