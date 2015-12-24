<?php

namespace CPSE\API\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('start','collot_datetime',array( 'pickerOptions' =>
            array('format' => 'dd/mm/yyyy',
                'weekStart' => 0,
                'autoclose' => true,
                'startView' => 'month',
                'minView' => 'month',
                'maxView' => 'decade',
                'todayBtn' => true,
                'todayHighlight' => false,
                'keyboardNavigation' => true,
                'language' => 'en',
                'forceParse' => true,
                'pickerPosition' => 'bottom-right',
                )))
            ->add('end','collot_datetime',array( 'pickerOptions' =>
            array('format' => 'dd/mm/yyyy',
                'weekStart' => 0,
                'autoclose' => true,
                'startView' => 'month',
                'minView' => 'month',
                'maxView' => 'decade',
                'todayBtn' => true,
                'todayHighlight' => false,
                'keyboardNavigation' => true,
                'language' => 'en',
                'forceParse' => true,
                'pickerPosition' => 'bottom-right',
                )))
            ->add('category')
            ->add('pm')
            ->add('description','textarea', array(
                'attr' => array(
                    'cols' => 80,
                    'rows' => 10,
                    'maxlength' => 10024
                ),
                'required' => false
            ))
            ->add('members', 'document', array('multiple' => true,
                                                'expanded' => false,
                                                'choices_as_values' => true,
                                                'class' => 'CPSEAPIUserBundle:APIUser'
            ))
            ->add('status')
            ->add('project_code')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CPSE\API\ProjectBundle\Document\Project'
        ));
    }

    public function getName()
    {
        return 'cpse_api_projectbundle_projecttype';
    }
}
