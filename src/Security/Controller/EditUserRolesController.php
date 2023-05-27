<?php

namespace Nadia\Bundle\NadiaRadBundle\Security\Controller;

use Nadia\Bundle\NadiaRadBundle\Security\Form\EditUserRolesType;
use Nadia\Bundle\NadiaRadBundle\Security\RequestModel\EditUserRoles;
use Nadia\Bundle\NadiaRadBundle\Security\Role\RoleHierarchyProvider;
use Nadia\Bundle\NadiaRadBundle\Security\Role\UserRoleUpdaterInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Twig\Environment;

final class EditUserRolesController
{
    /**
     * @param ServiceLocator $userProviders Use firewall name to get an UserProviderInterface instance.
     * @param ServiceLocator $userRoleUpdaters Use firewall name to get an UserRoleUpdaterInterface instance.
     */
    public function __construct(
        private readonly ServiceLocator $userProviders,
        private readonly ServiceLocator $userRoleUpdaters,
    )
    {
    }

    public function edit(
        string                $identifier,
        string                $firewall,
        Request               $request,
        RoleHierarchyProvider $roleHierarchyProvider,
        Environment           $twig,
        FormFactoryInterface  $formFactory,
        UrlGeneratorInterface $router,
    ): Response
    {
        /** @var UserProviderInterface $userProvider */
        $userProvider = $this->userProviders->get($firewall);
        $user = $userProvider->loadUserByIdentifier($identifier);
        $roleHierarchy = $roleHierarchyProvider->getRoleHierarchyRootNode($firewall);

        $data = new EditUserRoles($user->getRoles());
        $form = $formFactory->create(EditUserRolesType::class, $data, ['role_hierarchy_root_node' => $roleHierarchy]);
        $formView = $form->createView();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var UserRoleUpdaterInterface $updater */
                $updater = $this->userRoleUpdaters->get($firewall);

                $updater->update($user, $data->getRoles());

                return new RedirectResponse(
                    $router->generate('nadia_security_edit_user_roles', compact('identifier', 'firewall'))
                );
            }
        }

        $roleForms = [];
        foreach ($formView['roles'] as $role) {
            /** @var FormView $role */
            $roleForms[$role->vars['value']] = $role;
        }

        $viewData = [
            'identifier' => $identifier,
            'roleHierarchy' => $roleHierarchy,
            'form' => $formView,
            'roleForms' => $roleForms,
        ];

        return new Response($twig->render('@NadiaRad/security/edit-user-roles/index.html.twig', $viewData));
    }
}
