<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nadia\Bundle\NadiaRadBundle\Menu\MenuFactory;
use Nadia\Bundle\NadiaRadBundle\Menu\MenuProvider;

return static function(ContainerConfigurator $containerConfigurator) {
    $containerConfigurator->parameters()
        ->set('nadia.menu.cache_dir', '%kernel.cache_dir%/nadia/menus')
    ;

    $containerConfigurator->services()
        ->set(MenuProvider::class)->args([null, ''])
        ->set(MenuFactory::class)
            ->args([
                service(MenuProvider::class),
                service('security.token_storage'),
                service('security.authorization_checker'),
                service('knp_menu.factory'),
            ])
    ;
};
