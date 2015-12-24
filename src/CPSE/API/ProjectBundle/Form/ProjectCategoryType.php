<?php

namespace CPSE\API\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ODM\MongoDB\DocumentRepository;

class ProjectCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', 'textarea', array('attr' => array(
                                                    'cols' => 10,
                                                    'rows' => 10
            )))
            ->add('parent')
            ->add('documentCategory', 'document', array('class' => 'CPSEAPIProjectBundle:Document',
                'required' => true,
                'query_builder' => function(DocumentRepository $er) {
                return $er->createQueryBuilder()
                ->field('object_type')->equals(\CPSE\API\ProjectBundle\Document\Document::FOLDER);
                }
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CPSE\API\ProjectBundle\Document\ProjectCategory'
        ));
    }

    public function getName()
    {
        return 'cpse_api_projectbundle_projectcategorytype';
    }
}
