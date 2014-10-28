<?php
/**
 * Created by PhpStorm.
 * User: Acer
 * Date: 27.10.14
 * Time: 0:32
 */

namespace Nurolopher\BlogBundle\Controller;


use Nurolopher\BlogBundle\Form\Type\CommentType;
use Nurolopher\BlogBundle\Model\Comment;
use Nurolopher\BlogBundle\Model\CommentQuery;
use Nurolopher\BlogBundle\Model\PostQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CommentController extends Controller
{

    public function indexAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') || $this->get('security.context')->isGranted('ROLE_MODERATOR')) {

            $comments = CommentQuery::create()->find();
            return $this->render('NurolopherBlogBundle:Comment:index.html.twig', array('comments' => $comments));
        }else{
            throw new AccessDeniedException();
        }
    }

    public function newAction($id)
    {
        $post = PostQuery::create()->findPk($id);
        if (!$post) {
            throw new NotFoundHttpException();
        }
        $comment = new Comment();
        $comment->setUser($this->getUser());
        $comment->setPost($post);
        $form = $this->createForm(new CommentType(), $comment, array(
            'action' => $this->generateUrl('nurolopher_blog_comment_new', array('id' => $id))
        ));
        $form->handleRequest($this->get('request'));
        if ($form->isValid()) {
            $comment->save();
            $this->get('session')->set('success', 'You comment has been successfully added');
            return $this->redirect($this->generateUrl('nurolopher_blog_post_show', array('id' => $id)));
        }
        return $this->render('@NurolopherBlog/Comment/new.html.twig', array('form' => $form->createView()));
    }

    public function editAction($id)
    {
        $comment = CommentQuery::create()->findPk($id);
        if (!$comment) {
            throw new NotFoundHttpException();
        }
        $this->isValidUser($comment);
        $form = $this->createForm(new CommentType(), $comment);
        $form->handleRequest($this->get('request'));
        if ($form->isValid()) {
            $comment->save();
            return $this->redirect($this->generateUrl('nurolopher_blog_post_show', array('id' => $comment->getPost()->getId())));
        }
        return $this->render('@NurolopherBlog/Comment/edit.html.twig', array('form' => $form->createView()));
    }

    public function deleteAction($id)
    {
        $comment = CommentQuery::create()->findPk($id);
        if (!$comment) {
            throw new NotFoundHttpException();
        }
        $this->isValidUser($comment);
        try {
            $comment->delete();
        } catch (\PropelException $e) {
            $this->get('session')->getFlashBag()->set('error', 'Could not delete comment');
        }
        return $this->redirect($this->get('request')->headers->get('referer'));
    }

    public function isValidUser($comment)
    {
        $security = $this->get('security.context');
        if (!(is_object($this->getUser()) && ($comment->getUser()->getEmail() == $this->getUser()->getEmail() || $security->isGranted('ROLE_ADMIN') || $security->isGranted('ROLE_MODERATOR')))) {
            throw new AccessDeniedException();
        }
    }
}