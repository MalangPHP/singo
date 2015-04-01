<?php


namespace Singo\Tests\Provider;

use Singo\Tests\Entities\UserEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider
 * @package Singo\Tests\Provider
 */
class UserProvider implements UserProviderInterface
{
    /**
     * @param string $username
     * @return UserEntity
     */
    public function loadUserByUsername($username)
    {
        $user  = new UserEntity();
        $user->setUsername("admin");
        $user->setRoles(["ROLE_ADMIN"]);

        return $user;
    }

    /**
     * @param UserInterface $user
     * @return mixed
     */
    public function refreshuser(UserInterface $user)
    {
        return $this->loadByUsername($user->getUsername());
    }

    /**
     * @param string $class
     */
    public function supportsClass($class)
    {

    }
}

