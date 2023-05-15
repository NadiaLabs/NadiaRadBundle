<?php

/*
 * This file is part of the NadiaRadBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaRadBundle\Security\Voter;

use Nadia\Bundle\NadiaRadBundle\Security\Role\RoleHierarchyProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class RoleHierarchyVoter extends RoleVoter
{
    private RoleHierarchyProvider $roleHierarchyProvider;

    private RequestStack $requestStack;

    public function __construct(
        RoleHierarchyProvider $roleHierarchyProvider,
        RequestStack $requestStack,
        string $prefix = 'ROLE_'
    ) {
        $this->roleHierarchyProvider = $roleHierarchyProvider;
        $this->requestStack = $requestStack;

        parent::__construct($prefix);
    }

    public function vote(TokenInterface $token, mixed $subject, array $attributes): int
    {
        if ($this->requestStack->getMainRequest()->attributes->has('_firewall_context')) {
            return parent::vote($token, $subject, $attributes);
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }

    protected function extractRoles(TokenInterface $token): array
    {
        $firewallName = $this->getFirewallName();

        return $this->roleHierarchyProvider->get($firewallName)->getReachableRoleNames($token->getRoleNames());
    }

    private function getFirewallName(): bool
    {
        $firewallContextId = $this->requestStack->getMainRequest()->attributes->get('_firewall_context', '');

        return substr($firewallContextId, strrpos($firewallContextId, '.') + 1);
    }
}
