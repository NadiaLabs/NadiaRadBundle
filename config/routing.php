<?php

use Nadia\Bundle\NadiaRadBundle\Security\Controller\EditUserRolesController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->add('nadia_security_edit_user_roles', '/nadia/security/{firewall}/roles/edit/{identifier}/')
        ->controller([EditUserRolesController::class, 'edit'])
        ->methods(['GET', 'POST'])
    ;
};
