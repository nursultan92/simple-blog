<?php

namespace Nurolopher\BlogBundle\Controller;

use Nurolopher\BlogBundle\Form\Type\FilterPostType;
use Nurolopher\BlogBundle\Form\Type\PostType;
use Nurolopher\BlogBundle\Model\Category;
use Nurolopher\BlogBundle\Model\Post;
use Nurolopher\BlogBundle\Model\PostQuery;
use Nurolopher\BlogBundle\Model\UserQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PostController extends Controller
{
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page');
        $category = new Category();
        $form = $this->createForm(new FilterPostType(), $category, array('method' => 'get'));
        $form->handleRequest($this->get('request'));
        //die(print_r($category->getId()));

        $posts = PostQuery::create() /*->filterByCategory($category,\Criteria::EQUAL)*/
            ->orderByCreatedAt(\Criteria::DESC)->paginate($page, 2);
        //die(var_dump($posts));
        return $this->render('NurolopherBlogBundle:Post:index.html.twig', array('posts' => $posts, 'form' => $form->createView()));
    }

    public function newAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(new PostType(), $post);

        $form->handleRequest($request);
        if ($post->validate()) {
            $post->setUser($this->getUser());
            $post->save();
            $this->get('session')->getFlashBag()->add('notice', 'New Post has been successfully added');
            return $this->redirect($this->generateUrl('nurolopher_blog_post_index'));
        }
        return $this->render('NurolopherBlogBundle:Post:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function showAction($id)
    {
        $post = PostQuery::create()->findPk($id);
        if (!$post) {
            throw new \PropelException();
        }
        return $this->render('NurolopherBlogBundle:Post:show.html.twig', array('post' => $post));
    }

    public function editAction($id)
    {
        $post = PostQuery::create()->findPk($id);
        $form = $this->createForm(new PostType(), $post);
        if (!$post) {
            throw new \PropelException();
        }
        return $this->render('NurolopherBlogBundle:Post:edit.html.twig', array('form' => $form->createView()));
    }

    public function deleteAction($id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        $post = PostQuery::create()->findPk($id);
        if ($post) {
            $post->delete();
        }
        return $this->redirect($this->generateUrl('nurolopher_blog_post_index'));
    }
}
