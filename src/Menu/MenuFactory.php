<?php

/*
 * This file is part of the NadiaRadBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaRadBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class MenuFactory
{
    private MenuProvider $menuProvider;

    private TokenStorageInterface $tokenStorage;

    private AuthorizationCheckerInterface $authorizationChecker;

    private FactoryInterface $knpMenuFactory;

    /**
     * @var ItemInterface[]
     */
    private array $cachedKnpMenus = [];

    /**
     * MenuFactory constructor.
     *
     * @param MenuProvider $menuProvider
     * @param TokenStorageInterface $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param FactoryInterface $knpMenuFactory
     */
    public function __construct(
        MenuProvider                  $menuProvider,
        TokenStorageInterface         $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        FactoryInterface              $knpMenuFactory
    ) {
        $this->menuProvider = $menuProvider;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->knpMenuFactory = $knpMenuFactory;
    }

    public function create(string $menuName): ItemInterface
    {
        $cacheKey = $this->generateCacheKey($menuName);

        if (!isset($this->cachedKnpMenus[$cacheKey])) {
            $menu = $this->menuProvider->get($menuName);

            $this->filterGrantedMenuItems($menu['children']);

            $this->cachedKnpMenus[$cacheKey] = $this->buildMenuItems($menu);
        }

        return $this->cachedKnpMenus[$cacheKey];
    }

    private function filterGrantedMenuItems(array &$menu): void
    {
        foreach ($menu as $index => &$item) {
            if (empty($item['options']['roles'])) {
                continue;
            }

            $isGranted = false;

            foreach ($item['options']['roles'] as $role) {
                if ($isGranted = $this->authorizationChecker->isGranted($role)) {
                    break;
                }
            }

            if (!$isGranted) {
                unset($menu[$index]);
            } elseif (!empty($item['children'])) {
                $this->filterGrantedMenuItems($item['children']);
            }
        }
    }

    /**
     * Build Knp Menu items
     *
     * @param array $menus
     *
     * @return ItemInterface
     */
    private function buildMenuItems(array $menus): ItemInterface
    {
        $rootMenu = $this->knpMenuFactory->createItem($menus['root_title'], $menus['root_options']);

        $build = function (array $menus, ItemInterface $rootMenu) use (&$build) {
            foreach ($menus as $menu) {
                $childMenu = $rootMenu->addChild($menu['title'], $menu['options']);

                if (!empty($menu['children'])) {
                    $build($menu['children'], $childMenu);
                }
            }
        };

        $build($menus['children'], $rootMenu);

        return $rootMenu;
    }

    private function generateCacheKey($menuName): string
    {
        $token = $this->tokenStorage->getToken();
        $roles = [];

        if (!empty($token)) {
            $roles = $token->getRoleNames();
            sort($roles);
        }

        return $menuName . '-' . md5(join(',', $roles));
    }
}
