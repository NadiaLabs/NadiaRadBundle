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

/**
 * Using for building role hierarchy.
 *
 * Example:
 * <code>
 * // Root RoleHierarchyItem contains all roles.
 * $r = new RoleHierarchyItem('Super User', 'ROLE_SUPER_USER');
 * $r->children([
 *   $r->new('Project Management', 'ROLE_PROJECT')->children([
 *     $r->new('View Project', 'ROLE_PROJECT_VIEW'),
 *     $r->new('Edit Project', 'ROLE_PROJECT_EDIT')->children([
 *       $r->new('Create Project', 'ROLE_PROJECT_CREATE'),
 *       $r->new('Update Project', 'ROLE_PROJECT_UPDATE'),
 *     ]),
 *     $r->new('Delete Project', 'ROLE_PROJECT_DELETE'),
 *   ]),
 *   $r->new('Company Management', 'ROLE_COMPANY')->children([
 *     $r->new('View Company', 'ROLE_COMPANY_VIEW'),
 *     $r->new('Edit Company', 'ROLE_COMPANY_EDIT'),
 *   ]),
 * ]);
 * </code>
 */
final class RoleHierarchyItem
{
    public string $title = '';

    public string $roleName;

    /**
     * @var RoleHierarchyItem[]
     */
    public array $children = [];

    public function __construct(string $title, string $roleName)
    {
        $this->info($title, $roleName);
    }

    public function info(string $title, string $roleName): self
    {
        $this->title = $title;
        $this->roleName = $roleName;

        return $this;
    }

    /**
     * @param RoleHierarchyItem[] $children
     *
     * @return $this
     */
    public function children(array $children): self
    {
        $this->children = $children;

        return $this;
    }

    public function new(string $title, string $roleName): RoleHierarchyItem
    {
        return new RoleHierarchyItem($title, $roleName);
    }
}
