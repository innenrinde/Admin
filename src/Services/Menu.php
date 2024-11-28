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
                "title" => "Categories",
                "icon" => "Notebook",
                "children" => [
                    [
                        "title" => "Categories list",
                        "icon" => "Memo",
                        "route" => $this->router->generate("app_categories"),
                        "active" => $this->router->getContext()->getPathInfo() === "/categories",
                        "confirm" => false,
                    ],
                    [
                        "title" => "Add category",
                        "icon" => "CirclePlus",
                        "route" => $this->router->generate("app_categories_add"),
                        "active" => $this->router->getContext()->getPathInfo() === "/categories/add",
                        "confirm" => false,
                    ]
                ]
            ];
        }

        $menusList[] = [
            "title" => "Indicators",
            "icon" => "DataAnalysis",
            "children" => [
                [
                    "title" => "Indicators list",
                    "icon" => "DataLine",
                    "route" => $this->router->generate("app_indicators"),
                    "active" => $this->router->getContext()->getPathInfo() === "/indicators",
                    "confirm" => false,
                ],
                [
                    "title" => "Add indicator",
                    "icon" => "CirclePlus",
                    "route" => $this->router->generate("app_indicators_add"),
                    "active" => $this->router->getContext()->getPathInfo() === "/indicators/add",
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
                    "route" => $this->router->generate("app_account"),
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