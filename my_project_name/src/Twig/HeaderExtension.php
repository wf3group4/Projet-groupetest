<?php

namespace App\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use App\Repository\TagsRepository;

class HeaderExtension extends AbstractExtension
{
    private $twig;
    private $tagRepo;

    public function __construct(Environment $twig, TagsRepository $tagRepo)
    {
        $this->twig = $twig;
        $this->tagRepo = $tagRepo;
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
            'tags' => $this->tagRepo->findAll()
        ]);
    }
}
