<?php
/**
 * Created by PhpStorm.
 * User: Acer
 * Date: 26.10.14
 * Time: 3:55
 */

namespace Nurolopher\BlogBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title')
            ->add('submit','submit',array('attr'=>array('class'=>'btn btn-primary')));
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