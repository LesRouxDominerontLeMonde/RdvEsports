<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    function index (Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        // $user = new User();
        // $user->setEmail('John@doe.fr')
        //     ->setUsername('JohnDoe')
        //     ->setPassword($hasher->hashPassword($user, '0000'))
        //     ->setRoles(['ROLE_ADMIN']);
        // $em->persist($user);
        // $em->flush();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
