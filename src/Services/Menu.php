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
                "title" => "Tasks",
                "icon" => "Tickets",
                "children" => [
                    [
                        "title" => "Tasks list",
                        "icon" => "Memo",
                        "route" => $this->router->generate("app_tasks"),
                        "active" => $this->router->getContext()->getPathInfo() === "/tasks",
                        "confirm" => false,
                    ],
                    [
                        "title" => "Add task",
                        "icon" => "CirclePlus",
                        "route" => $this->router->generate("app_tasks_add"),
                        "active" => $this->router->getContext()->getPathInfo() === "/tasks/add",
                        "confirm" => false,
                    ]
                ]
            ]
        ];

        if ($this->security->getUser()->isAdmin()) {
            $menusList[] = [
                "title" => "Users",
                "icon" => "User",
                "children" => [
                    [
                        "title" => "Users list",
                        "icon" => "Memo",
                        "route" => $this->router->generate("app_users"),
                        "active" => $this->router->getContext()->getPathInfo() === "/users",
                        "confirm" => false,
                    ],
                    [
                        "title" => "Add user",
                        "icon" => "CirclePlus",
                        "route" => $this->router->generate("app_users_add"),
                        "active" => $this->router->getContext()->getPathInfo() === "/users/add",
                        "confirm" => false,
                    ]
                ]
            ];
        }

        $menusList[] = [
            "title" => "Account",
            "icon" => "Setting",
            "children" => [
                [
                    "title" => "Edit profile",
                    "icon" => "User",
                    "route" => $this->router->generate("app_users"),
                    "active" => $this->router->getContext()->getPathInfo() === "/account",
                    "confirm" => false,
                ],
                [
                    "title" => "Sign out",
                    "icon" => "SwitchButton",
                    "route" => $this->router->generate("app_logout"),
                    "confirm" => true,
                ]
            ]
        ];

        return $menusList;
    }
}