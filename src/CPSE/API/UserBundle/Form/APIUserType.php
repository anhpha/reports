<?php

namespace CPSE\API\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CPSE\API\UserBundle\Document\APIUser;

class APIUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roles', 'choice', array(
                'choices' => array( 
                                   'Editor' => APIUser::DOC_EDITOR,
                                    'Deputy Manager' => APIUser::DEP_MANAGER,
                                    'Manager' => APIUser::MANAGER,
                                    'Deputy Director' => APIUser::DEP_DIRECTOR,
                                    'Director' => APIUser::DIRECTOR),
                // always include this
                'choices_as_values' => true,
                'translation_domain' => 'apiuser',
                'expanded' => true,
                'multiple' => true
            ))
            ->add('email', 'email', array('required' => true))
            ->add('enabled', 'checkbox', array('data' => true))
            ->add('locked', 'checkbox', array(
                            'required' => false
            ))
            ->add('fullName')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CPSE\API\UserBundle\Document\APIUser'
        ));
    }

    public function getName()
    {
        return 'cpse_api_userbundle_apiusertype';
    }
}
