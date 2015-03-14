<?php
namespace Scrumbe\Bundle\UserBundle\Security\User;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserProvider implements UserProviderInterface
{
    private $userService;

    public function __construct($userService)
    {
        $this->userService = $userService;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->userService->getUserByUsername($username);

        if (!empty($user))
        {
            $id = $user['id'];
            $password = $user['password'];
            if(!empty($user['salt']))
            {
                $user['salt'];
            }
            else
            {
                $salt = null;
            }
            $roles = array();

            return new User($id, $username, $password, $salt, $roles);
        }

        throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User)
        {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Scrumbe\Bundle\UserBundle\Security\User\User';
    }
}