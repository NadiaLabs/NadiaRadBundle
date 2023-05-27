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
    private array $cachedRoleHierarchies = [];

    private array $cachedRoleHierarchyRootNodes = [];

    public function __construct(ServiceLocator $roleHierarchyBuilders, string $cacheDir)
    {
        $this->roleHierarchyBuilders = $roleHierarchyBuilders;
        $this->cacheDir = $cacheDir;
    }

    public function get(string $firewallName): RoleHierarchy
    {
        if (!isset($this->cachedRoleHierarchies[$firewallName])) {
            $fs = new Filesystem();
            $filepath = $this->cacheDir . '/' . $firewallName . '.cache';

            if ($fs->exists($filepath)) {
                $hierarchy = unserialize(file_get_contents($filepath));
            } else {
                $hierarchy = $this->getWithoutCache($firewallName);

                file_put_contents($filepath, serialize($this->cachedRoleHierarchies[$firewallName]));
            }

            $this->cachedRoleHierarchies[$firewallName] = new RoleHierarchy($hierarchy);
        }

        return $this->cachedRoleHierarchies[$firewallName];
    }

    public function getWithoutCache(string $firewallName): array
    {
        $root = $this->getRoleHierarchyRootNode($firewallName);
        $hierarchy = [];

        $this->convertRootNodeToHierarchyArray($root, $hierarchy);

        return $hierarchy;
    }

    public function getRoleHierarchyRootNode(string $firewallName): RoleNode
    {
        if (!isset($this->cachedRoleHierarchyRootNodes[$firewallName])) {
            /** @var RoleHierarchyBuilderInterface $builder */
            $builder = $this->roleHierarchyBuilders->get($firewallName);

            $this->cachedRoleHierarchyRootNodes[$firewallName] = $builder->build();
        }

        return $this->cachedRoleHierarchyRootNodes[$firewallName];
    }

    private function convertRootNodeToHierarchyArray(RoleNode $node, array &$result): void
    {
        if (empty($node->children)) {
            return;
        }

        $result[$node->roleName] = [];

        foreach ($node->children as $child) {
            $result[$node->roleName][] = $child->roleName;

            $this->convertRootNodeToHierarchyArray($child, $result);
        }
    }
}
