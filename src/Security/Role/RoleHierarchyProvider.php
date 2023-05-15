<?php

/*
 * This file is part of the NadiaRadBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaRadBundle\Security\Role;

use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\Role\RoleHierarchy;

final class RoleHierarchyProvider
{
    private ServiceLocator $roleHierarchyBuilders;

    private string $cacheDir;

    /**
     * @var RoleHierarchy[]
     */
    private array $cachedRoleHierarchy = [];

    public function __construct(ServiceLocator $roleHierarchyBuilders, string $cacheDir)
    {
        $this->roleHierarchyBuilders = $roleHierarchyBuilders;
        $this->cacheDir = $cacheDir;
    }

    public function get(string $firewallName): RoleHierarchy
    {
        if (!isset($this->cachedRoleHierarchy[$firewallName])) {
            $fs = new Filesystem();
            $filepath = $this->cacheDir . '/' . $firewallName . '.cache';

            if ($fs->exists($filepath)) {
                $this->cachedRoleHierarchy[$firewallName] = unserialize(file_get_contents($filepath));
            } else {
                $this->cachedRoleHierarchy[$firewallName] = $this->getWithoutCache($firewallName);

                file_put_contents($filepath, serialize($this->cachedRoleHierarchy[$firewallName]));
            }
        }

        return $this->cachedRoleHierarchy[$firewallName];
    }

    public function getWithoutCache(string $firewallName): RoleHierarchy
    {
        /** @var RoleHierarchyBuilder $builder */
        $builder = $this->roleHierarchyBuilders->get($firewallName);
        $root = new RoleHierarchyItem('Root', 'ROLE_ROOT');
        $roleHierarchy = [];

        $builder->build($root);

        $this->resolve($root, $roleHierarchy);

        return new RoleHierarchy($roleHierarchy);
    }

    private function resolve(RoleHierarchyItem $item, array &$result): void
    {
        if (empty($item->children)) {
            return;
        }

        $result[$item->roleName] = [];

        foreach ($item->children as $child) {
            $result[$item->roleName][] = $child->roleName;

            $this->resolve($child, $result);
        }
    }
}
