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

interface RoleHierarchyBuilder
{
    /**
     * Build role hierarchy items.
     *
     * Example:
     * <code>
     * #[AutoconfigureTag('nadia.tag.role_hierarchy', ['firewall_name' => 'main'])]
     * class ExampleMenuLoader implement MenuBuilder {
     *   public function build(RoleHierarchyItem $root): void {
     *     // Root RoleHierarchyItem contains all roles.
     *     $r = $root->info('Super User', 'ROLE_SUPER_USER');
     *     $r->children([
     *       $r->new('Project Management', 'ROLE_PROJECT')->children([
     *         $r->new('View Project', 'ROLE_PROJECT_VIEW'),
     *         $r->new('Edit Project', 'ROLE_PROJECT_EDIT')->children([
     *           $r->new('Create Project', 'ROLE_PROJECT_CREATE'),
     *           $r->new('Update Project', 'ROLE_PROJECT_UPDATE'),
     *         ]),
     *         $r->new('Delete Project', 'ROLE_PROJECT_DELETE'),
     *       ]),
     *       $r->new('Company Management', 'ROLE_COMPANY')->children([
     *         $r->new('View Company', 'ROLE_COMPANY_VIEW'),
     *         $r->new('Edit Company', 'ROLE_COMPANY_EDIT'),
     *       ]),
     *     ]);
     *   }
     * }
     * </code>
     *
     * @param RoleHierarchyItem $root
     *
     */
    public function build(RoleHierarchyItem $root): void;
}
