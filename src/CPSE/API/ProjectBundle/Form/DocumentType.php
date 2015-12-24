<?php

namespace CPSE\API\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
                        'attr' => array(
                            'maxlength' => 255
                        )
            ))
            ->add('file', 'file', array(
                'required' => false
            ))
            ->add('description','textarea', array(
                'attr' => array(
                    'cols' => 100,
                    'rows' => 20,
                    'maxlength' => 10024
                ),
                'required' => false
            ))
            ->add('type','hidden', array('data' => \CPSE\API\ProjectBundle\Document\Document::FILE))
        ;
            
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CPSE\API\ProjectBundle\Document\Document'
        ));
    }

    public function getName()
    {
        return 'cpse_api_projectbundle_documenttype';
    }
}
