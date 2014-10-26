<?php
/**
 * Created by PhpStorm.
 * User: Acer
 * Date: 25.10.14
 * Time: 22:46
 */

namespace Nurolopher\BlogBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title')
            ->add('body')
            ->add('categories','model',array(
                'class'=>'Nurolopher\BlogBundle\Model\Category',
                'property'=>'title',
                'multiple'=>true,
                'expanded'=>true
            ))
            ->add('submit','submit',array('attr'=>array('class'=>'btn btn-primary')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'=>'Nurolopher\BlogBundle\Model\Post',
        ));
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'post';
    }
}