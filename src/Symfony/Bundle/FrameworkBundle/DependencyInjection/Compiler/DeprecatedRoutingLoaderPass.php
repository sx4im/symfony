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
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Loader\AttributeRouteControllerLoader as BaseAttributeRouteControllerLoader;

/**
 * Makes the "routing.loader.attribute" service an instance of the deprecated
 * FrameworkBundle loader class, so that type-hints against it keep working
 * until the class is removed in 9.0.
 *
 * The deprecated class extends the component one, so type-hints against
 * either class are satisfied.
 */
class DeprecatedRoutingLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('routing.loader.attribute')) {
            return;
        }

        $definition = $container->getDefinition('routing.loader.attribute');

        // Only swap the class when it is still the component one; a class
        // explicitly configured by the application must be left alone.
        if (BaseAttributeRouteControllerLoader::class !== $definition->getClass()) {
            return;
        }

        $definition->setClass(AttributeRouteControllerLoader::class);
    }
}
