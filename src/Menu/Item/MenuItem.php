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

class MenuItem
{
    public string $title = '';
    public array $options = [];

    /**
     * @var MenuItem[]
     */
    public array $children = [];

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function options(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param MenuItem[] $children
     *
     * @return $this
     */
    public function children(array $children): self
    {
        $this->children = $children;

        return $this;
    }
}
