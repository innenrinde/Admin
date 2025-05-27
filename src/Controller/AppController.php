<?php

namespace App\Controller;

use App\Services\Menu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    #[Route('/app', name: 'app')]
    public function index(): Response
    {
        return $this->render('app/index.html.twig');
    }

    #[Route('/app/settings', name: 'app_settings', methods: ['GET'])]
    public function settings(Menu $menuService): JsonResponse
    {
        return new JsonResponse([
            'title' => 'SyncArt',
            'menus' => $menuService->menus(),
        ], Response::HTTP_OK);
    }
}
