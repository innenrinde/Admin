<?php

namespace App\Services;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Menu
{
    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Create menus list
     * @return array[]
     */
    function menus(): array
    {
        return [
            [
                'title' => 'Tasks List',
                'icon' => 'Tickets',
                'route' => $this->router->generate("app_tasks"),
            ],
            [
                'title' => 'Users List',
                'icon' => 'User',
                'route' => $this->router->generate("app_users"),
            ],
            [
                'title' => 'Sign Out',
                'icon' => 'SwitchButton',
                'route' => $this->router->generate("app_logout"),
            ]
        ];
    }
}