<?php

namespace Nurolopher\BlogBundle\Controller;

use Nurolopher\BlogBundle\Form\Type\FilterPostType;
use Nurolopher\BlogBundle\Form\Type\PostType;
use Nurolopher\BlogBundle\Model\Category;
use Nurolopher\BlogBundle\Model\CategoryQuery;
use Nurolopher\BlogBundle\Model\CommentQuery;
use Nurolopher\BlogBundle\Model\Post;
use Nurolopher\BlogBundle\Model\PostQuery;
use Nurolopher\BlogBundle\Model\UserQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PostController extends Controller
{
    public function indexAction(Request $request, $category_id)
    {
        $page = $request->query->get('page');
        $categories = CategoryQuery::create()->find();
        $posts = PostQuery::create();
        if (is_numeric($category_id))
            $posts = $posts->filterByCategory(CategoryQuery::create()->findPk($category_id));
        $posts = $posts->orderByCreatedAt(\Criteria::DESC)->paginate($page, 2);
        return $this->render('NurolopherBlogBundle:Post:index.html.twig', array('posts' => $posts, 'categories' => $categories,'current_category'=>$category_id));
    }

    public function newAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        $post = new Post();
        $form = $this->createForm(new PostType(), $post);

        $form->handleRequest($request);
        if ($post->validate() && $form->isValid()) {
            $post->setUser($this->getUser());
            $post->save();
            $this->get('session')->getFlashBag()->add('success', 'New Post has been successfully added');
            return $this->redirect($this->generateUrl('nurolopher_blog_post_show',array('id'=>$post->getId())));
        }
        return $this->render('NurolopherBlogBundle:Post:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function showAction($id)
    {
        $post = PostQuery::create()->findPk($id);
        if (!$post) {
            throw new NotFoundHttpException();
        }
        return $this->render('NurolopherBlogBundle:Post:show.html.twig', array('post' => $post));
    }

    public function editAction(Request $request, $id)
    {

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        $post = PostQuery::create()->findPk($id);
        if (!$post) {
            throw new NotFoundHttpException();
        }
        $form = $this->createForm(new PostType(), $post);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $post->save();
            $this->get('session', 'Post has been successfully updated');
            return $this->redirect($this->generateUrl('nurolopher_blog_post_show', array('id' => $id)));
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
            try {
                $post->delete();
            } catch (\PropelException $e) {
                $this->get('session')->getFlashBag()->set('error', 'Could not delete post');
            }
        } else {
            throw new NotFoundHttpException();
        }
        return $this->redirect($this->generateUrl('nurolopher_blog_post_index'));
    }
}
