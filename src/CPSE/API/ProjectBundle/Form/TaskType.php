<?php

namespace CPSE\API\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CPSE\API\ProjectBundle\Document\Task;
use CPSE\API\ProjectBundle\Form\DocumentSimpleType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('desription', 'textarea', array(
                            'attr' => array(
                                'cols' => 10,
                                'rows' => 10
                            )
            ))
            ->add('from','collot_datetime',array( 'pickerOptions' =>
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
            ->add('to','collot_datetime',array( 'pickerOptions' =>
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
            ->add('assigned_to')
            ->add('related_docs', 'collection', array(
                        'type' => new DocumentSimpleType(),
                        'allow_add'    => true,
                        'allow_delete' => true,
                        'by_reference' => false,
            ))    
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CPSE\API\ProjectBundle\Document\Task'
        ));
    }

    public function getName()
    {
        return 'cpse_api_projectbundle_tasktype';
    }
}
