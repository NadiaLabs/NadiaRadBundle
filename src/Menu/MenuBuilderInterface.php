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

use Nadia\Bundle\NadiaRadBundle\Menu\Item\MenuRootItem;

interface MenuBuilderInterface
{
    /**
     * Build menu items.
     *
     * Example:
     * <code>
     * #[AutoconfigureTag('nadia.tag.menu_builder', ['menu_name' => 'main'])]
     * class ExampleMenuLoader implement MenuBuilderInterface
     * {
     *   public function build(MenuRootItem $root): void
     *   {
     *     $m = $root;
     *     $m
     *       ->children([
     *         $m->item('Dashboard'),
     *         $m->item('Articles')
     *           ->options([
     *             'route' => 'articles',
     *           ])
     *           ->children([
     *             $m->item('Create Article')
     *               ->options([
     *                 'route' => 'article-create',
     *               ]),
     *             $m->item('Edit Article')->options([
     *               'route' => 'article-edit',
     *             ]),
     *           ]),
     *         $m->item('Tags')
     *           ->children([
     *             $m->item('Create Tag'),
     *             $m->item('Edit Tag'),
     *           ]),
     *       ]);
     *   }
     * }
     * </code>
     *
     * @param MenuRootItem $root
     *
     */
    public function build(MenuRootItem $root): void;
}
