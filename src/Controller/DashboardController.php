<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\ObjectData;
use App\Entity\User;
use App\Services\Menu;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DashboardController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UrlGeneratorInterface $router
    ) {
    }

    #[Route('/dashboard/settings', name: 'app_settings', methods: ['GET'])]
    public function settings(Menu $menuService): JsonResponse
    {
        return new JsonResponse([
            'title' => 'SyncArt',
            'menus' => $menuService->menus(),
        ], Response::HTTP_OK);
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        $categories = $this->em->getRepository(Category::class)->count();

        $museums = $this->em->getRepository(ObjectData::class)->count();
        $users = $this->em->getRepository(User::class)->count(['isActive' => true]);

        $presentation = $this->render('dashboard/presentation.html.twig', ['key' => 'value']);

        return $this->render('dashboard/index.html.twig', [
            'charts' => [
                [
                    'label' => "Available categories",
                    'count' => $categories,
                    'link' => $this->router->generate("app_categories"),
                    'color' => 'c1'
                ],
                [
                    'label' => "Parsed museum objects",
                    'link' => $this->router->generate("app_objectdata"),
                    'count' => $museums,
                    'color' => 'c2'
                ],
                [
                    'label' => "Active users",
                    'link' => $this->router->generate("app_users"),
                    'count' => $users,
                    'color' => 'c3'
                ]
            ],
            'presentation' => $presentation->getContent()
        ]);
    }
}
