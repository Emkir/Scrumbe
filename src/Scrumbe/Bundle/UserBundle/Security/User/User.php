<?php
namespace Scrumbe\Bundle\UserBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    private $id;
    private $username;
    private $password;
    private $salt;
    private $roles;
    private $email;
    private $firstname;
    private $lastname;
    private $avatar;
    private $domain;
    private $business;

    public function __construct($id, $username, $password, $salt, array $roles, $email, $firstname, $lastname, $avatar, $domain, $business)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt;
        $this->roles = $roles;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->avatar = $avatar;
        $this->domain = $domain;
        $this->business = $business;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function getBusiness()
    {
        return $this->business;
    }

    public function eraseCredentials()
    {
    }
}