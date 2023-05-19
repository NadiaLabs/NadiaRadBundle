<?php

/*
 * This file is part of the NadiaRadBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaRadBundle\Menu;

use Nadia\Bundle\NadiaRadBundle\Menu\Item\MenuItem;
use Nadia\Bundle\NadiaRadBundle\Menu\Item\MenuRootItem;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Filesystem\Filesystem;

final class MenuProvider
{
    private ServiceLocator $menuBuilders;

    private string $cacheDir;

    private array $cachedMenus = [];

    /**
     * MenuProvider constructor.
     *
     * @param ServiceLocator $menuBuilders
     * @param string $cacheDir Cache directory: '%kernel.cache_dir%/nadia/menus'
     */
    public function __construct(ServiceLocator $menuBuilders, string $cacheDir)
    {
        $this->menuBuilders = $menuBuilders;
        $this->cacheDir = rtrim($cacheDir, '/ ');
    }

    /**
     * Get menu parameters
     *
     * @param string $menuName
     *
     * @return array
     */
    public function get(string $menuName): array
    {
        if (!isset($this->cachedMenus[$menuName])) {
            $fs = new Filesystem();
            $filepath = $this->cacheDir . '/' . $menuName . '.cache';

            if ($fs->exists($filepath)) {
                $this->cachedMenus[$menuName] = unserialize(file_get_contents($filepath));
            } else {
                $this->cachedMenus[$menuName] = $this->getWithoutCache($menuName);

                file_put_contents($filepath, serialize($this->cachedMenus[$menuName]));
            }
        }

        return $this->cachedMenus[$menuName];
    }

    /**
     * Get menu parameters without cache.
     *
     * @param string $menuName
     *
     * @return array
     */
    public function getWithoutCache(string $menuName): array
    {
        if (!$this->menuBuilders->has($menuName)) {
            throw new \InvalidArgumentException(sprintf('Menu name "%s" is not exists.', $menuName));
        }

        /** @var MenuBuilderInterface $menuBuilder */
        $menuBuilder = $this->menuBuilders->get($menuName);
        $root = new MenuRootItem();

        $menuBuilder->build($root);

        return $this->resolve($root);
    }

    private function resolve(MenuRootItem $root): array
    {
        $menu = [
            'root_title' => $root->title,
            'root_options' => $root->options,
            'children' => [],
        ];
        $defaultItemOptions = $root->defaultItemOptions;

        $toArray = function (array &$menu, MenuItem $item) use (&$toArray, $defaultItemOptions) {
            foreach ($item->children as $index => $child) {
                $menu[$index] = [
                    'title' => $child->title,
                    'options' => [],
                    'children' => [],
                ];
                $options = array_merge($defaultItemOptions, $child->options);

                foreach ($options as $key => $value) {
                    if (strlen($key) > 1 && '@' === $key[0]) {
                        $menu[$index]['options']['extras'][substr($key, 1)] = $value;
                        unset($options[$key]);
                    } else {
                        $menu[$index]['options'][$key] = $value;
                    }
                }

                if (!empty($child->children)) {
                    $toArray($menu[$index]['children'], $child);
                }
            }
        };

        $toArray($menu['children'], $root);

        return $menu;
    }
}
