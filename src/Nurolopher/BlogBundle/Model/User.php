<?php

namespace Nurolopher\BlogBundle\Model;

use Nurolopher\BlogBundle\Model\om\BaseUser;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

class User extends BaseUser implements UserInterface
{
    public function  __construct()
    {
        $this->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        parent::__construct();
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        return $this->getGroup()->getRoles();
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->getEmail();
        // TODO: Implement getUsername() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public
    function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
