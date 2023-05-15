<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nadia\Bundle\NadiaRadBundle\Security\Role\RoleHierarchyProvider;
use Nadia\Bundle\NadiaRadBundle\Security\Voter\RoleHierarchyVoter;

return static function(ContainerConfigurator $containerConfigurator) {
    $containerConfigurator->services()
        ->set(RoleHierarchyProvider::class)->args([null, ''])
        ->set(RoleHierarchyVoter::class)
            ->args([
                service(RoleHierarchyProvider::class),
                service('request_stack'),
            ])
            ->tag('security.voter', ['priority' => 255])
    ;
};
