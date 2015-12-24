<?php

namespace CPSE\API\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DocumentSearchQueryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->setMethod('GET')
        ->add('sortedBy', 'hidden', array('required' => false))
        ->add('order', 'hidden', array('required' => false))
        ->add('currentPage', 'hidden', array('required' => false))
        ->add('year', 'number', array('required' => false))
        ->add('category', 'text', array('required' => false))
        ->add('q', 'text', array('required' => false))
        ->add('project', 'document', array(
                         'class' => 'CPSEAPIProjectBundle:Project',
                         'choice_label' => 'name',
                         'required' => false
        ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CPSE\API\ProjectBundle\Classes\DocumentSearchQuery',
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return '';
    }
}
