<?php
/**
 * Created by PhpStorm.
 * User: Acer
 * Date: 25.10.14
 * Time: 23:18
 */

namespace Nurolopher\BlogBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GroupType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('roles', 'choice', array(
                'choices' => array(
                    'choices' => array(
                        'ROLE_ADMIN' => 'Admin',
                        'ROLE_MODERATOR' => 'Moderator',
                        'ROLE_USER' => 'User',
                    ),

                ),

                'multiple'=>true,
                'expanded'=>true
            ))
            ->add('submit','submit',array('attr'=>array('class'=>'btn btn-primary')));
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'group';
    }
}