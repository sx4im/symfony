<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler;

use Symfony\Bundle\FrameworkBundle\Routing\AttributeRouteControllerLoader;
use Symfony\Bundle\FrameworkBundle\Routing\RedirectableCompiledUrlMatcher;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Loader\AttributeRouteControllerLoader as BaseAttributeRouteControllerLoader;
use Symfony\Component\Routing\Matcher\RedirectableCompiledUrlMatcher as BaseRedirectableCompiledUrlMatcher;

/**
 * Keeps the deprecated FrameworkBundle routing classes on the routing services.
 *
 * The routing services moved to symfony/routing in 8.2, but their classes are
 * still available as deprecated subclasses in FrameworkBundle. While those
 * exist, the services keep using the FrameworkBundle classes, so that
 * type-hints against them keep working and trigger a deprecation instead
 * of a TypeError.
 *
 * @internal
 */
class AttributeRouteControllerLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition('routing.loader.attribute')
            && BaseAttributeRouteControllerLoader::class === $container->getDefinition('routing.loader.attribute')->getClass()
        ) {
            $container->getDefinition('routing.loader.attribute')->setClass(AttributeRouteControllerLoader::class);
        }

        if ($container->hasDefinition('router.default')) {
            $options = $container->getDefinition('router.default')->getArgument(2);
            if (($options['matcher_class'] ?? null) === BaseRedirectableCompiledUrlMatcher::class) {
                $options['matcher_class'] = RedirectableCompiledUrlMatcher::class;
                $container->getDefinition('router.default')->replaceArgument(2, $options);
            }
        }
    }
}
