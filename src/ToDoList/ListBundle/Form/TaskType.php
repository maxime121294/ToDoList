<?php

namespace ToDoList\ListBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                "label" => "Titre",
                "required" => true,
                "attr" => array("placeholder" => "Obligatoire"),
            ))
            ->add('description', 'textarea', array(
                "label" => "Description",
                "required" => false,
                "attr" => array("placeholder" => "Facultatif"),
            ))
            ->add('dueDate', 'datetime', array(
                "label" => "Date butoir",
                "required" => false,
                "attr" => array("placeholder" => "Facultatif"),
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ToDoList\ListBundle\Entity\Task'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'todolist_listbundle_task';
    }
}
