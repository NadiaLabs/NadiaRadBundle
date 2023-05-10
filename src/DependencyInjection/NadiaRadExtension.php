<?php

/*
 * This file is part of the NadiaRadBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaRadBundle\DependencyInjection;

use Nadia\Bundle\NadiaRadBundle\Menu\MenuProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class NadiaRadExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ .'/../../config'));

        $this->loadMenuServices($loader, $container);
    }

    private function loadMenuServices(PhpFileLoader $loader, ContainerBuilder $container): void
    {
        $loader->load('menu.php');

        $menuCacheDir = $container->getParameter('kernel.cache_dir') . '/nadia/menus';
        $container->setParameter('nadia.menu.cache_dir', $menuCacheDir);

        if (!file_exists($menuCacheDir)) {
            mkdir($menuCacheDir, 0755, true);
        }

        $container->getDefinition(MenuProvider::class)->setArgument(1, $menuCacheDir);
    }
}
