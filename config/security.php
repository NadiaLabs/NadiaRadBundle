<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nadia\Bundle\NadiaRadBundle\Security\Controller\EditUserRolesController;
use Nadia\Bundle\NadiaRadBundle\Security\Role\RoleHierarchyProvider;
use Nadia\Bundle\NadiaRadBundle\Security\Voter\RoleHierarchyVoter;

return static function(ContainerConfigurator $containerConfigurator) {
    $containerConfigurator->parameters()
        ->set('nadia.security.role_hierarchy.cache_dir', '%kernel.cache_dir%/nadia/role_hierarchy')
    ;

    $containerConfigurator->services()
        ->set(RoleHierarchyProvider::class)
            ->args([null, ''])
        ->set(RoleHierarchyVoter::class)
            ->args([
                service(RoleHierarchyProvider::class),
                service('request_stack'),
            ])
            ->tag('security.voter', ['priority' => 255])
        ->set(EditUserRolesController::class)
            ->args([null, null])
            ->tag('controller.service_arguments')
    ;
};
