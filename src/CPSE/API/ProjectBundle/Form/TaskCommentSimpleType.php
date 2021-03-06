<?php

namespace CPSE\API\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskCommentSimpleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', 'textarea', array(
                                    'required' => true,
                                    'attr' => array(
                                        'cols' => 10,
                                        'rows'  => 10
            )))
            ->add('action', 'hidden')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CPSE\API\ProjectBundle\Document\TaskComment'
        ));
    }

    public function getName()
    {
        return 'cpse_api_projectbundle_taskcommenttype';
    }
}
