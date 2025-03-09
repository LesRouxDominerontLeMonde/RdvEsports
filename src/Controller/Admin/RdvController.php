<?php

namespace App\Controller\Admin;

use App\Entity\Rdv;
use App\Repository\RdvRepository;
use App\Repository\VilleRepository;
use App\Form\RdvType;
use App\Security\Voter\RdvVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/admin', name: 'admin.')]
// #[IsGranted('ROLE_ADMIN')]
final class RdvController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/rdvs', name: 'rdv.index')]
    public function index(Request $request, RdvRepository $repository, PaginatorInterface $paginator, VilleRepository $villeRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 3); 
        $place = $request->query->get('place');
        $distance = $request->query->getInt('distance', 5);

        $pagination = $this->search($request, $repository, $paginator, $page, $limit, $place, $distance, $villeRepository);

        $this->addFlash('info', sprintf('%d RDV trouvé(s)', $pagination->getTotalItemCount()));

        return $this->render('admin/rdv/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/rdv/{id}/edit', name: 'rdv.edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(RdvVoter::EDIT, subject: 'rdv')]
    public function edit(Rdv $rdv, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RdvType::class, $rdv);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rdv->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();
            $this->addFlash('success', 'Le RDV a bien été modifié.');
            return $this->redirectToRoute('admin.rdv.index');
        }
        return $this->render('admin/rdv/edit.html.twig', [
            'rdv' => $rdv,
            'form' => $form
        ]);
    }

    #[Route('/rdv/new', name: 'rdv.new', methods: ['GET', 'POST'])]
    #[IsGranted(RdvVoter::CREATE)]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $rdv = new Rdv();
        $form = $this->createForm(RdvType::class, $rdv);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rdv->setCreatedAt(new \DateTimeImmutable());
            $rdv->setUpdatedAt(new \DateTimeImmutable());
            $rdv->setAuthor($this->security->getUser()); // Set the logged-in user as the author
            $em->persist($rdv);
            $em->flush();
            $this->addFlash('success', 'Le RDV a bien été créé.');
            return $this->redirectToRoute('admin.rdv.index');
        }
        return $this->render('admin/rdv/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/rdv/{id}', name: 'rdv.delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(RdvVoter::EDIT, subject: 'rdv')] // Same than 'edit' but for delete
    public function delete(Rdv $rdv, EntityManagerInterface $em): Response
    {
        $em->remove($rdv);
        $em->flush();
        $this->addFlash('success', 'Le RDV a bien été supprimé.');
        return $this->redirectToRoute('admin.rdv.index');
    }

    #[Route('/rdv/ville_autocomplete', name: 'rdv.ville_autocomplete', methods: ['GET'])]
    public function ville_autocomplete(Request $request, VilleRepository $villeRepository): JsonResponse
    {
        $villeQuery = $request->query->get('ville');
        if (!$villeQuery) {
            return $this->json([]);
        }
        $villes = $villeRepository->createQueryBuilder('v')
            ->where('v.nom LIKE :villeQuery')
            ->setParameter('villeQuery', $villeQuery.'%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
        
        $result = array_map(function($ville) {
            return [
                'id' => $ville->getCodeInsee(),
                'text' => sprintf('%s (%s)', $ville->getNom(), $ville->getCodePostal()),
            ];
        }, $villes);

        return $this->json($result);
    }

    private function search(Request $request, RdvRepository $repository, PaginatorInterface $paginator, int $page, int $limit, ?string $place, int $distance, VilleRepository $villeRepository)
    {
        if ($place) {
            $ville = $villeRepository->findOneBy(['nom' => $place]);
            if ($ville) {
                $query = $repository->findCityAround($ville->getLatitude(), $ville->getLongitude(), $distance);
            } else {
                // ??
                $query = $repository->findByPlaceQuery($place);
            }
        } else {
            $query = $repository->findAllRdv();
        }

        return $paginator->paginate(
            $query,
            $page,
            $limit
        );
    }
}