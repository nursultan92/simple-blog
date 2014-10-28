<?php
/**
 * Created by PhpStorm.
 * User: Acer
 * Date: 26.10.14
 * Time: 0:06
 */

namespace Nurolopher\BlogBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserAdminType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('email','email')
            ->add('firstname','text')
            ->add('lastname','text',array('required'=>false))
            ->add('group','model',array(
                'class'=>'Nurolopher\BlogBundle\Model\Group',
                'property'=>'name'
            ))
            ->add('password','repeated',array(
                'type'=>'password',
                'required'=>false,
                'invalid_message'=>'The password fields must match',
                'first_options'=>array('label'=>'Password','required'=>false),
                'second_options'=>array('label'=>'Repeat Password','required'=>false),
            ))

            ->add('submit','submit',array('attr'=>array('class'=>'btn')));
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'=>'Nurolopher\BlogBundle\Model\User'
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'user_signup';
    }
}