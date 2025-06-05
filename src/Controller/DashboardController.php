<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\ObjectData;
use App\Entity\User;
use App\Services\Menu;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DashboardController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Menu $menu
    ) {
    }

    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
    public function index(): Response
    {
        $categories = $this->em->getRepository(Category::class)->count();
        $museums = $this->em->getRepository(ObjectData::class)->count();
        $users = $this->em->getRepository(User::class)->count(['isActive' => true]);

        $presentation = $this->render('dashboard/presentation.html.twig');

        $charts = [
            [
                'label' => "Available categories",
                'count' => $categories,
                'menu' => $this->menu->menuCategoryList,
                'color' => 'c1'
            ],
            [
                'label' => "Parsed museum objects",
                'count' => $museums,
                'menu' => $this->menu->menuObjectList,
                'color' => 'c2'
            ],
            [
                'label' => "Active users",
                'count' => $users,
                'menu' => $this->menu->menuUserList,
                'color' => 'c3'
            ]
        ];

        return new JsonResponse([
            'charts' => $charts,
            'presentation' => $presentation->getContent(),
        ], Response::HTTP_OK);
    }
}
