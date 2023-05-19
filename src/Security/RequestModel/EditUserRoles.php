<?php

namespace Nadia\Bundle\NadiaRadBundle\Security\RequestModel;

final class EditUserRoles
{
    public function __construct(private array $roles)
    {
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
