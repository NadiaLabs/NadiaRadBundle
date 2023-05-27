<?php

namespace Nadia\Bundle\NadiaRadBundle\Security\Form;

use Nadia\Bundle\NadiaRadBundle\Security\RequestModel\EditUserRoles;
use Nadia\Bundle\NadiaRadBundle\Security\Role\RoleNode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EditUserRolesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $rolesChoices = [];
        $this->prepareRolesChoices($options['role_hierarchy_root_node'], $rolesChoices);

        $builder
            ->add('roles', ChoiceType::class, [
                'choices' => $rolesChoices,
                'expanded' => true,
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditUserRoles::class,
        ]);

        $resolver->setRequired('role_hierarchy_root_node');
        $resolver->setAllowedTypes('role_hierarchy_root_node', RoleNode::class);
    }

    private function prepareRolesChoices(RoleNode $node, array &$choices): void
    {
        $choices[$node->roleName] = $node->roleName;

        foreach ($node->children as $child) {
            $this->prepareRolesChoices($child, $choices);
        }
    }
}
