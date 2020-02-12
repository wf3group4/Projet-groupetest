<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use App\Entity\Users;

class VuesSubscriber implements EventSubscriberInterface
{
    private $vues;

    public function __construct($vues)
    {
        $this->vues = $vues;
    }

    public function onKernerRequest($event)
    {
        $user = $this->vues->getUsers(); // On récupère l'utilisateur de la session en cours
        if ($user){
            $this->vues++;
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kerner.request' => 'onKernerRequest',
        ];
    }
}
