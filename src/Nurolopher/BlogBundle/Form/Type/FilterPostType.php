<?php
/**
 * Created by PhpStorm.
 * User: Acer
 * Date: 26.10.14
 * Time: 14:27
 */

namespace Nurolopher\BlogBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FilterPostType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id','model',array(
            'class'=>'Nurolopher\BlogBundle\Model\Category',
            'multiple'=>false,
            'expanded'=>false
        ))
        ->add('submit','submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        return array(
            'class'=>'Nurolopher\BlogBundle\Model\Category',
            'csrf_protection'=>'false'
        );
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'category';
    }
}