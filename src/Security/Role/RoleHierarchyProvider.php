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
    private ServiceLocator $roleTreeBuilders;

    private string $cacheDir;

    /**
     * @var RoleHierarchy[]
     */
    private array $cachedRoleHierarchy = [];

    public function __construct(ServiceLocator $roleTreeBuilders, string $cacheDir)
    {
        $this->roleTreeBuilders = $roleTreeBuilders;
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
        $tree = $this->getRoleTree($firewallName);
        $hierarchy = [];

        $this->resolve($tree, $hierarchy);

        return new RoleHierarchy($hierarchy);
    }

    public function getRoleTree(string $firewallName): RoleNode
    {
        /** @var RoleTreeBuilderInterface $builder */
        $builder = $this->roleTreeBuilders->get($firewallName);
        $root = new RoleNode('Root', 'ROLE_ROOT');

        $builder->build($root);

        return $root;
    }

    private function resolve(RoleNode $node, array &$result): void
    {
        if (empty($node->children)) {
            return;
        }

        $result[$node->roleName] = [];

        foreach ($node->children as $child) {
            $result[$node->roleName][] = $child->roleName;

            $this->resolve($child, $result);
        }
    }
}
