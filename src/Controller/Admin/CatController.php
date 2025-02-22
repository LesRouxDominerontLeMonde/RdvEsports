<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Cat;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\CatType;
use App\Repository\CatRepository;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin.')]
#[IsGranted('ROLE_ADMIN')]
final class CatController extends AbstractController
{
    #[Route('/cat', name: 'cat.index')]
    public function index(CatRepository $repository): Response
    {
        $categories = $repository->findCategoriesWithRdvCount();
        return $this->render('admin/cat/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/cat/{id}/edit', name: 'cat.edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Cat $cat, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CatType::class, $cat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cat->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();
            $this->addFlash('success', 'La catégorie a bien été modifiée.');
            return $this->redirectToRoute('admin.cat.index');
        }
        return $this->render('admin/cat/edit.html.twig', [
            'cat' => $cat,
            'form' => $form
        ]);
    }

    #[Route('/cat/new', name: 'cat.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $cat = new Cat();
        $form = $this->createForm(CatType::class, $cat);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cat->setCreatedAt(new \DateTimeImmutable());
            $cat->setUpdatedAt(new \DateTimeImmutable());
            $em->persist($cat);
            $em->flush();
            $this->addFlash('success', 'La catégorie a bien été créée.');
            return $this->redirectToRoute('admin.cat.index');
        }
        return $this->render('admin/cat/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route ('/cat/{id}/delete', name: 'cat.delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Cat $cat, EntityManagerInterface $em): Response
    {
        $em->remove($cat);
        $em->flush();
        $this->addFlash('success', 'La catégorie a bien été supprimée.');
        return $this->redirectToRoute('admin.cat.index');
    }
}
