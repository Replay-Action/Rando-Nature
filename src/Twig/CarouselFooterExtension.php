<?php


namespace App\Twig;


use App\Repository\PartenaireRepository;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CarouselFooterExtension extends AbstractExtension
{
    /**
     * @var PartenaireRepository
     */
    private $partenaireRepository;
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(PartenaireRepository $partenaireRepository, Environment $twig){
        $this->partenaireRepository = $partenaireRepository;
        $this->twig = $twig;
    }

    public function getFunctions(): array
    {
    return [
        new TwigFunction('carousel',[$this, 'getCarousel'], ['is_safe' => ['html']])
    ];
}

    public function getCarousel(): string{
         $partenaire = $this->partenaireRepository->findPartenaire();
         return $this->twig->render('inc/nav_footer.html.twig',[
             'partenaires' => $partenaire
         ]);
    }
}