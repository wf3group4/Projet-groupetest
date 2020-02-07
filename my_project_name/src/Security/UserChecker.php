<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Entity\Users;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof Users) {
            return;
        }

        
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof Users) {
            return;
        }

        // user account is expired, the user may be notified
        if ($user->getActive() != 1) {
            throw new CustomUserMessageAuthenticationException('Veuillez valider votre compte par email.');
        }
    }
}