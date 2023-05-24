<?php

/*
 * This file is part of the NadiaRadBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaRadBundle\Menu\Item;

/**
 * Example:
 * <code>
 * $m = new MenuRootItem();
 * $m->defaultItemOptions([
 *     'attributes' => ['class' => 'nav-item'],
 *     '>translation_domain' => false,
 *   ])
 *   ->children([
 *     $m->item('Dashboard'),
 *     $m->item('Articles')
 *       ->options([
 *         'route' => 'articles',
 *       ])
 *       ->children([
 *         $m->item('Create Article')
 *           ->options([
 *             'route' => 'article-create',
 *           ]),
 *         $m->item('Edit Article')
 *           ->options([
 *             'route' => 'article-edit',
 *           ]),
 *       ]),
 *     $m->item('Tags')
 *       ->children([
 *         $m->item('Create Tag'),
 *         $m->item('Edit Tag'),
 *       ]),
 *   ]);
 * </code>
 */
final class MenuRootItem extends MenuItem
{
    public array $defaultItemOptions = [];

    public function __construct(string $title = 'root')
    {
        parent::__construct($title);
    }

    public function defaultItemOptions(array $options): self
    {
        $this->defaultItemOptions = $options;

        return $this;
    }

    public function item(string $title): MenuItem
    {
        return new MenuItem($title);
    }
}
