<?php

namespace ToDoList\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add your custom field
        $builder->add('nom', 'text', array(
                "label" => "Nom",
                "required" => true,
            ))
            ->add('prenom', 'text', array(
                "label" => "Prénom",
                "required" => true,
            ));
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'todolist_user_registration';
    }
}