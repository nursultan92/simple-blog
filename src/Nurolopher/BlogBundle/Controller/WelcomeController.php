<?php
/**
 * Created by PhpStorm.
 * User: Acer
 * Date: 28.10.14
 * Time: 17:32
 */

namespace Nurolopher\BlogBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WelcomeController extends Controller
{

    public function indexAction()
    {
       return $this->redirect($this->generateUrl('nurolopher_blog_post_index'));
    }

} 