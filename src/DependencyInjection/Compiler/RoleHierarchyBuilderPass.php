<?php

/*
 * This file is part of the NadiaRadBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaRadBundle\DependencyInjection\Compiler;

use Nadia\Bundle\NadiaRadBundle\Security\Role\RoleHierarchyProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class RoleHierarchyBuilderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $taggedServices = $container->findTaggedServiceIds('nadia.tag.role_hierarchy_builder');
        $cacheDir = $container->getParameter('nadia.security.role_hierarchy.cache_dir');
        $fs = new Filesystem();
        $finder = new Finder();
        $map = [];

        foreach ($finder->files()->in($cacheDir) as $file) {
            $fs->remove($file);
        }

        foreach ($taggedServices as $id => $tags) {
            $firewallName = $tags[0]['firewall_name'];
            $map[$firewallName] = new Reference($id);

            $className = $container->getDefinition($id)->getClass();
            // Register RoleHierarchyBuilder file for resource tracking
            $container->fileExists((new \ReflectionClass($className))->getFileName());
        }

        $container->getDefinition(RoleHierarchyProvider::class)
            ->setArgument(0, ServiceLocatorTagPass::register($container, $map));
    }
}
