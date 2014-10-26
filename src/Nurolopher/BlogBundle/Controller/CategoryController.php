<?php
/**
 * Created by PhpStorm.
 * User: Acer
 * Date: 26.10.14
 * Time: 3:53
 */

namespace Nurolopher\BlogBundle\Controller;


use Nurolopher\BlogBundle\Form\Type\CategoryType;
use Nurolopher\BlogBundle\Form\Type\FilterPostType;
use Nurolopher\BlogBundle\Model\Category;
use Nurolopher\BlogBundle\Model\CategoryQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CategoryController extends Controller
{

    public function indexAction()
    {
        $categories = CategoryQuery::create()->find();
        return $this->render('NurolopherBlogBundle:Category:index.html.twig', array('categories' => $categories));
    }

    public function newAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(new CategoryType(), $category);
        $form->handleRequest($request);
        if ($category->validate() && $form->isValid()) {
            $category->save();
            $this->get('session')->getFlashBag()->add('notice', 'New category has been successfully added');
            return $this->redirect($this->generateUrl('nurolopher_blog_category_index'));
        }
        return $this->render('NurolopherBlogBundle:Category:new.html.twig', array('form' => $form->createView()));
    }

    public function editAction($id)
    {
        $category = CategoryQuery::create()->findPk($id);
        $form = $this->createForm(new CategoryType(), $category);
        $form->handleRequest($this->get('request'));
        if ($category->validate() && $form->isValid()) {
            $category->save();
        }
        return $this->render('@NurolopherBlog/Category/new.html.twig', array('form' => $form->createView()));
    }

    public function deleteAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        $category = CategoryQuery::create()->findPk($id);
        if(is_object($category)){
            try{
                $category->delete();
            }catch ( \PropelException $e){
                $this->get('session')->getFlashBag()->set('notice','Please delete related posts first');
            }
            if($category->isDeleted()){
                $this->get('session')->getFlashBag()->set('success','Category has beeen successfully deleted');
            }
        }
        return $this->redirect($this->generateUrl('nurolopher_blog_category_index'));

    }

    public function filterFormAction(\PropelCollection $collection)
    {
        $form = $this->createForm(new FilterPostType(), $this->get('request')->query->all());
        return $this->render('@NurolopherBlog/Category/filter_form.html.twig', array('form' => $form->createView()));
    }
} 