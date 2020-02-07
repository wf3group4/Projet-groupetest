<?php

namespace App\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Repository\UsersRepository;

class HeaderExtension extends AbstractExtension
{
    private $twig;
    private $usersRepo;

    public function __construct(Environment $twig, UsersRepository $usersRepo)
    {
        $this->twig = $twig;
        $this->usersRepo = $usersRepo;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('displayHeader', [$this, 'displayHeader'], ['is_safe' => ['html']]),
            new TwigFunction('displayFooter', [$this, 'displayFooter'], ['is_safe' => ['html']]),
        ];
    }

    public function displayHeader()
    {
        return $this->twig->render('front/header.html.twig', [

        ]);
    }

    public function displayFooter()
    {
        return $this->twig->render('front/footer.html.twig', [

        ]);
    }
}
