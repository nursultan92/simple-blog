<?php

namespace Nurolopher\BlogBundle\Controller;

use Nurolopher\BlogBundle\Form\Type\PostType;
use Nurolopher\BlogBundle\Model\Post;
use Nurolopher\BlogBundle\Model\PostQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PostController extends Controller
{
    public function indexAction()
    {
        $posts = PostQuery::create()->orderByCreatedAt(\Criteria::DESC)->find();
        return $this->render('NurolopherBlogBundle:Post:index.html.twig', array('posts' => $posts));
    }

    public function newAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(new PostType(), $post);

        $form->handleRequest($request);
        if($post->validate()){
            $post->setUser($this->getUser());
            $post->save();
        }
        return $this->render('NurolopherBlogBundle:Post:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
