<?php

namespace CPSE\API\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DocumentCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent')
            ->add('name')
            ->add('description','textarea', array(
                'attr' => array(
                    'cols' => 100,
                    'rows' => 20,
                    'maxlength' => 1024
                ),
                'required' => false
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CPSE\API\ProjectBundle\Document\DocumentCategory'
        ));
    }

    public function getName()
    {
        return 'cpse_api_projectbundle_documentcategorytype';
    }
}
