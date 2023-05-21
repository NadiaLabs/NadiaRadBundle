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
use Nadia\Bundle\NadiaRadBundle\DependencyInjection\Compiler\UserRoleUpdaterPass;
use Nadia\Bundle\NadiaRadBundle\Menu\MenuProvider;
use Nadia\Bundle\NadiaRadBundle\Security\Role\RoleHierarchyProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class NadiaRadBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new MenuBuilderPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 10);
        $container->addCompilerPass(new RoleTreeBuilderPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 10);
        $container->addCompilerPass(new UserRoleUpdaterPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 10);
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $loader = new PhpFileLoader($builder, new FileLocator(__DIR__ . '/../config'));

        $this->loadMenuServices($loader, $builder);
        $this->loadSecurityServices($loader, $builder);
    }

    private function loadMenuServices(PhpFileLoader $loader, ContainerBuilder $builder): void
    {
        $loader->load('menu.php');

        $menuCacheDir = $builder->getParameter('nadia.menu.cache_dir');
        if (!file_exists($menuCacheDir)) {
            mkdir($menuCacheDir, 0755, true);
        }

        $builder->getDefinition(MenuProvider::class)->setArgument(1, $menuCacheDir);
    }

    private function loadSecurityServices(PhpFileLoader $loader, ContainerBuilder $builder): void
    {
        $loader->load('security.php');

        $roleTreeCacheDir = $builder->getParameter('nadia.security.role_tree.cache_dir');
        if (!file_exists($roleTreeCacheDir)) {
            mkdir($roleTreeCacheDir, 0755, true);
        }

        $builder->getDefinition(RoleHierarchyProvider::class)->setArgument(1, $roleTreeCacheDir);
    }
}
