<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\FrameworkBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\DeprecatedRoutingLoaderPass;
use Symfony\Bundle\FrameworkBundle\Routing\AttributeRouteControllerLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Loader\AttributeDirectoryLoader;
use Symfony\Component\Routing\Loader\AttributeRouteControllerLoader as BaseAttributeRouteControllerLoader;

class DeprecatedRoutingLoaderPassTest extends TestCase
{
    public function testLoaderClassIsSwappedToDeprecatedSubclass()
    {
        $container = new ContainerBuilder();
        $container->register('routing.loader.attribute', BaseAttributeRouteControllerLoader::class);

        (new DeprecatedRoutingLoaderPass())->process($container);

        $this->assertSame(AttributeRouteControllerLoader::class, $container->getDefinition('routing.loader.attribute')->getClass());
    }

    public function testPassDoesNothingWhenTheLoaderServiceIsMissing()
    {
        $container = new ContainerBuilder();

        (new DeprecatedRoutingLoaderPass())->process($container);

        $this->assertFalse($container->hasDefinition('routing.loader.attribute'));
    }

    public function testCustomLoaderClassIsKept()
    {
        $container = new ContainerBuilder();
        $container->register('routing.loader.attribute', AttributeDirectoryLoader::class);

        (new DeprecatedRoutingLoaderPass())->process($container);

        $this->assertSame(AttributeDirectoryLoader::class, $container->getDefinition('routing.loader.attribute')->getClass());
    }
}
