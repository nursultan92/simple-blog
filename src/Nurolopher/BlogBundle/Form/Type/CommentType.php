<?php

namespace Nurolopher\BlogBundle\Form\Type;

use Propel\PropelBundle\Form\BaseAbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CommentType extends BaseAbstractType
{
    protected $options = array(
        'data_class' => 'Nurolopher\BlogBundle\Model\Comment',
        'name'       => 'comment',
    );

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('body')
            ->add('postId','hidden')
            ->add('userId','hidden')
            ->add('submit','submit',array('attr'=>array('class'=>'btn btn-primary')));
    }
}
