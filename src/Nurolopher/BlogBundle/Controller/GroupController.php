<?php
/**
 * Created by PhpStorm.
 * User: Acer
 * Date: 25.10.14
 * Time: 23:30
 */

namespace Nurolopher\BlogBundle\Controller;


use Nurolopher\BlogBundle\Form\Type\GroupType;
use Nurolopher\BlogBundle\Model\Group;
use Nurolopher\BlogBundle\Model\GroupQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GroupController extends Controller
{
    public function indexAction()
    {
        $groups = GroupQuery::create()->find();
        return $this->render('NurolopherBlogBundle:Group:index.html.twig', array('groups' => $groups));
    }

    public function newAction(Request $request)
    {
        $group = new Group();
        $form = $this->createForm(new GroupType(), $group);
        $form->handleRequest($request);
        if ($group->validate() && $form->isValid()) {
            $group->save();
            $this->get('session')->getFlashBag()->add('notice','New group has been successfully created');
            return $this->redirect($this->generateUrl('nurolopher_blog_group_index'));
        }
        return $this->render('NurolopherBlogBundle:Group:new.html.twig', array('form' => $form->createView()));
    }

    public function editAction($id)
    {
        $group = GroupQuery::create()->findPk($id);
        if(!$group){
            throw new \PropelException();
        }
        $form = $this->createForm(new GroupType(),$group);
        $form->handleRequest($this->get('request'));
        return $this->render('NurolopherBlogBundle:Group:edit.html.twig',array('form'=>$form->createView()));
    }
} 