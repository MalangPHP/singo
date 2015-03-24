<?php


namespace Singo\Providers;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserServiceProvider implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        return [
            "username" => "pras",
            "password" => "encrypted"
        ];
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {

    }
}

// EOF
