<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompteController extends AbstractController
{
    #[Route('/compte', name: 'compte')]
    public function index(): Response
    {
        return $this->render('compte/index.html.twig');
    }

    #[Route('/compte/mes-annonces', name: 'compte.mes_annonces')]
    public function mesAnnonces(): Response
    {
        return $this->render('compte/mes_annonces.html.twig');
    }
}
