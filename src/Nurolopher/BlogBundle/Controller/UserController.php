<?php
/**
 * Created by PhpStorm.
 * User: Acer
 * Date: 25.10.14
 * Time: 23:05
 */

namespace Nurolopher\BlogBundle\Controller;


use Nurolopher\BlogBundle\Form\Type\UserType;
use Nurolopher\BlogBundle\Model\GroupQuery;
use Nurolopher\BlogBundle\Model\PostQuery;
use Nurolopher\BlogBundle\Model\User;
use Nurolopher\BlogBundle\Model\UserQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

class UserController extends Controller
{

    public function indexAction()
    {
        $users = UserQuery::create()->find();
        return $this->render('NurolopherBlogBundle:User:index.html.twig', array('users' => $users));
    }

    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(new UserType(), $user);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());

            $user->setPassword($password);
            $user->save();
            return $this->redirect($this->generateUrl('nurolopher_blog_homepage'));
        }
        return $this->render('NurolopherBlogBundle:User:new.html.twig', array('form' => $form->createView()));
    }

    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContextInterface::AUTHENTICATION_ERROR
            );
        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);

        return $this->render(
            '@NurolopherBlog/User/login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $lastUsername,
                'error' => $error,
            )
        );
    }

    public function showAction($id)
    {
        $user = UserQuery::create()->findPk($id);
        if (!$user) {
            throw new \PropelException();
        }
        return $this->render('NurolopherBlogBundle:User:show.html.twig', array('user' => $user));
    }

    public function editAction($id)
    {
        $user = UserQuery::create()->findPk($id);
        $form = $this->createForm(new UserType(), $user);
        if (!$user) {
            throw new \PropelException();
        }
        return $this->render('NurolopherBlogBundle:User:edit.html.twig', array('form' => $form->createView()));
    }

    public function deleteAction($id)
    {
        $user = UserQuery::create()->findPk($id);
        if ($user) {
            try {
                $user->delete();
            } catch (\PropelException $e) {
                throw new \PropelException();
            }
        }

        return $this->redirect($this->generateUrl('nurolopher_blog_user_index'));
    }

    public function registerAction()
    {
        return $this->render('@NurolopherBlog/Post/register.html.twig', array(''));
    }
} 