<?php


namespace Singo\Providers;

use Singo\App\Entities\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserServiceProvider implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        $user = new User();
        $user->setUsername("admin");
        $user->setRoles(["ROLE_ADMIN"]);
        return $user;
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
