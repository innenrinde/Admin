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
        $menusList = [];

        if ($this->security->getUser()->isAdmin()) {

            $menusList[] = [
                "title" => "Dashboard",
                "icon" => "list",
                "children" => [
                    [
                        "title" => "Summary data",
                        "icon" => "chart-simple",
                        "route" => $this->router->generate("app_dashboard"),
                        "active" => $this->router->getContext()->getPathInfo() === "/dashboard",
                        "confirm" => false,
                    ],
                ]
            ];

            $menusList[] = [
                "title" => "Categories",
                "icon" => "list",
                "children" => [
                    [
                        "title" => "Categories list",
                        "icon" => "list",
                        "route" => $this->router->generate("app_categories"),
                        "active" => $this->router->getContext()->getPathInfo() === "/categories",
                        "confirm" => false,
                    ],
                    [
                        "title" => "Add category",
                        "icon" => "plus",
                        "route" => $this->router->generate("app_categories_add"),
                        "active" => $this->router->getContext()->getPathInfo() === "/categories/add",
                        "confirm" => false,
                    ]
                ]
            ];
        }

        $menusList[] = [
            "title" => "Data objects",
            "icon" => "DataAnalysis",
            "children" => [
                [
                    "title" => "Data objects list",
                    "icon" => "chart-column",
                    "route" => $this->router->generate("app_objectdata"),
                    "active" => $this->router->getContext()->getPathInfo() === "/objectdata",
                    "confirm" => false,
                ],
                [
                    "title" => "Add a data object",
                    "icon" => "plus",
                    "route" => $this->router->generate("app_objectdata_add"),
                    "active" => $this->router->getContext()->getPathInfo() === "/objectdata/add",
                    "confirm" => false,
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
                        "icon" => "users",
                        "route" => $this->router->generate("app_users"),
                        "active" => $this->router->getContext()->getPathInfo() === "/users",
                        "confirm" => false,
                    ],
                    [
                        "title" => "Add user",
                        "icon" => "plus",
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
                    "icon" => "user",
                    "route" => $this->router->generate("app_account"),
                    "active" => $this->router->getContext()->getPathInfo() === "/account",
                    "confirm" => false,
                ],
                [
                    "title" => "Sign out",
                    "icon" => "power-off",
                    "route" => $this->router->generate("app_logout"),
                    "confirm" => true,
                ]
            ]
        ];

        return $menusList;
    }
}