<?php

namespace App\Controller;

use App\Repository\RdvRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
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
    public function mesAnnonces(Request $request, RdvRepository $repository, PaginatorInterface $paginator, Security $security): Response
    {
        $page = $request->query->getInt('page', 1); 
        $limit = $request->query->getInt('limit', 6);
        $userId = $security->getUser()->getId();
        $pagination = $this->search($repository, $paginator, $page, $limit, $userId);
        return $this->render('compte/mes_annonces.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    public function search(RdvRepository $repository, PaginatorInterface $paginator, int $page, int $limit, $userId)
    {
        $query = $repository->searchAllFromUser($userId);
        return $paginator->paginate($query, $page, $limit);
    }
}
