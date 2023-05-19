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

use Symfony\Component\Security\Core\User\UserInterface;

interface UserRoleUpdaterInterface
{
    /**
     * Update user roles.
     *
     * Example:
     * <code>
     * #[AutoconfigureTag('nadia.tag.user_role_updater', ['firewall_name' => 'main'])]
     * class ExampleUserRoleUpdater implement UserRoleUpdaterInterface
     * {
     *   private ManagerRegistry $doctrine;
     *
     *   public function update(UserInterface $user, array $newRoles): void
     *   {
     *     $om = $this->doctrine->getManagerForClass(get_class($user));
     *
     *     $user->setRoles($newRoles);
     *
     *     $om->persist($user);
     *     $om->flush();
     *   }
     * }
     * </code>
     *
     * @param UserInterface $user
     * @param string[] $newRoles
     */
    public function update(UserInterface $user, array $newRoles): void;
}
