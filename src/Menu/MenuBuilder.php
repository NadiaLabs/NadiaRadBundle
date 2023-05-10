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

interface MenuBuilder
{
    /**
     * Build menu items.
     *
     * Example:
     * <code>
     * #[AutoconfigureTag('nadia.tag.menu_builder', ['menu_name' => 'main'])]
     * class ExampleMenuLoader implement MenuBuilder {
     *   public function build(MenuRootItem $root): void {
     *     $m = $root;
     *     $m
     *       ->children([
     *         $m->item('Dashboard'),
     *         $m->item('Articles')
     *           ->children([
     *             $m->item('Create Article'),
     *             $m->item('Edit Article'),
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
