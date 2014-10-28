<?php
/**
 * Created by PhpStorm.
 * User: Acer
 * Date: 25.10.14
 * Time: 23:05
 */

namespace Nurolopher\BlogBundle\Controller;


use Nurolopher\BlogBundle\Form\Type\UserAdminType;
use Nurolopher\BlogBundle\Form\Type\UserEditType;
use Nurolopher\BlogBundle\Form\Type\UserSignupType;
use Nurolopher\BlogBundle\Form\Type\UserType;
use Nurolopher\BlogBundle\Model\GroupQuery;
use Nurolopher\BlogBundle\Model\User;
use Nurolopher\BlogBundle\Model\UserQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;

class UserController extends Controller
{

    public function indexAction()
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $users = UserQuery::create()->find();
        return $this->render('NurolopherBlogBundle:User:index.html.twig', array('users' => $users));
    }

    public function newAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
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
        if ($user == $this->getUser() || $this->get('security.context')->isGranted('ROLE_ADMIN')) {

            return $this->render('NurolopherBlogBundle:User:show.html.twig', array('user' => $user));
        } else {
            throw new AccessDeniedException();
        }
    }

    public function editAction($id)
    {

        $user = UserQuery::create()->findPk($id);
        if (!$user) {
            throw new NotFoundHttpException();
        }
        $originalPassword = $user->getPassword();
        $formType = new UserAdminType();
        if ($this->getUser() == $user && $this->get('security.context')->isGranted('ROLE_USER')) {
            $formType = new UserEditType();
        }
        $form = $this->createForm($formType, $user);
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') || (is_object($this->getUser()) && $this->getUser() == $user)) {
            $form->handleRequest($this->get('request'));
            if ($form->isValid()) {
                if (is_null($user->getPassword())) {
                    $user->setPassword($originalPassword);
                } else {
                    $factory = $this->get('security.encoder_factory');
                    $encoder = $factory->getEncoder($user);
                    $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());

                    $user->setPassword($password);
                }
                $user->save();
                $this->get('session')->getFlashBag()->set('success', 'User information has been successfully updated');
                return $this->redirect($this->generateUrl('nurolopher_blog_user_show', array('id' => $user->getId())));
            }
        } else {
            throw new AccessDeniedException();
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
                $this->get('session')->getFlashBag()->set('error', 'Could not delete');
                return $this->redirect($this->generateUrl('nurolopher_blog_user_index'));
            }
            $this->get('session')->getFlashBag()->set('success', 'User has been successfully deleted');
        } else {
            throw new NotFoundHttpException();
        }

        return $this->redirect($this->generateUrl('nurolopher_blog_user_index'));
    }

    public function signupAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(new UserSignupType(), $user);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $group = GroupQuery::create()->findOneByRoles(array('ROLE_USER'));
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());

            $user->setPassword($password);
            $user->setGroup($group);
            $user->save();
            $this->get('session')->set('success', 'You have been succussfully signed up. You can login now');
            return $this->redirect($this->generateUrl('login'));
        }
        return $this->render('@NurolopherBlog/Post/register.html.twig', array('form' => $form->createView()));
    }
} 