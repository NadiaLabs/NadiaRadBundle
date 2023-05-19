<?php

/*
 * This file is part of the NadiaRadBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaRadBundle;

use Nadia\Bundle\NadiaRadBundle\DependencyInjection\Compiler\MenuBuilderPass;
use Nadia\Bundle\NadiaRadBundle\DependencyInjection\Compiler\RoleTreeBuilderPass;
use Nadia\Bundle\NadiaRadBundle\DependencyInjection\NadiaRadExtension;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class NadiaRadBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new MenuBuilderPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 10);
        $container->addCompilerPass(new RoleTreeBuilderPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 10);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new NadiaRadExtension();
    }
}
