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

use Nadia\Bundle\NadiaRadBundle\Security\Controller\EditUserRolesController;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class UserRoleUpdaterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $taggedServices = $container->findTaggedServiceIds('nadia.tag.user_role_updater');
        $map = [];

        foreach ($taggedServices as $id => $tags) {
            $firewallName = $tags[0]['firewall_name'];
            $map[$firewallName] = new Reference($id);
        }

        $container->getDefinition(EditUserRolesController::class)
            ->setArgument(1, ServiceLocatorTagPass::register($container, $map));
    }
}
