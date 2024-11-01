<?php

namespace App\Services;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Menu
{
    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $router;

    /**
     * @var Security
     */
    private Security $security;

    public function __construct(UrlGeneratorInterface $router, Security $security)
    {
        $this->router = $router;
        $this->security = $security;
    }

    /**
     * Create menus list
     * @return array[]
     */
    function menus(): array
    {
        $menusList = [
            [
                'title' => 'Tasks List',
                'icon' => 'Tickets',
                'route' => $this->router->generate("app_tasks"),
                'active' => $this->router->getContext()->getPathInfo() === "/tasks",
                'confirm' => false,
            ]
        ];

        if ($this->security->getUser()->isAdmin()) {
            $menusList[] = [
                'title' => 'Users List',
                'icon' => 'User',
                'route' => $this->router->generate("app_users"),
                'active' => $this->router->getContext()->getPathInfo() === "/users",
                'confirm' => false,
            ];
        }

        $menusList[] = [
            'title' => 'Sign Out',
            'icon' => 'SwitchButton',
            'route' => $this->router->generate("app_logout"),
            'active' => false,
            'confirm' => true,
        ];

        return $menusList;
    }
}