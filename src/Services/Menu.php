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
                        "component" => "Dashboard"
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
                        "component" => "Table",
                        "url" => [
                            "get" => $this->router->generate("app_categories_list"),
                            "post" => $this->router->generate("app_categories_edit"),
                            "delete" => $this->router->generate("app_categories_delete"),
                        ],
                    ],
                    [
                        "title" => "Add category",
                        "icon" => "plus",
                        "route" => $this->router->generate("app_categories_add"),
                        "active" => $this->router->getContext()->getPathInfo() === "/categories/add",
                        "confirm" => false,
                        "component" => "Form",
                        "url" => [
                            "get" => $this->router->generate('app_categories_list'),
                            "put" => $this->router->generate('app_categories_create')
                        ]
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
                    "component" => "Table",
                    "url" => [
                        "get" => $this->router->generate('app_objectdata_list'),
                        "put" => $this->router->generate('app_objectdata_create'),
                        "delete" => $this->router->generate('app_objectdata_delete')
                    ]
                ],
                [
                    "title" => "Add a data object",
                    "icon" => "plus",
                    "route" => $this->router->generate("app_objectdata_add"),
                    "active" => $this->router->getContext()->getPathInfo() === "/objectdata/add",
                    "confirm" => false,
                    "component" => "Form",
                    "url" => [
                        "get" => $this->router->generate('app_objectdata_list'),
                        "put" => $this->router->generate('app_objectdata_create')
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
                        "icon" => "users",
                        "route" => $this->router->generate("app_users"),
                        "active" => $this->router->getContext()->getPathInfo() === "/users",
                        "confirm" => false,
                        "component" => "Table",
                        "url" => [
                            "get" => $this->router->generate("app_users_list"),
                            "post" => $this->router->generate("app_users_edit"),
                            "delete" => $this->router->generate("app_users_delete"),
                        ],
                    ],
                    [
                        "title" => "Add user",
                        "icon" => "plus",
                        "route" => $this->router->generate("app_users_add"),
                        "active" => $this->router->getContext()->getPathInfo() === "/users/add",
                        "confirm" => false,
                        "component" => "Form",
                        "url" => [
                            "get" => $this->router->generate('app_users_list'),
                            "put" => $this->router->generate('app_users_create')
                        ]
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
                    "component" => "Form",
                    "url" => [
                        "get" => $this->router->generate('app_users_list'),
                        "put" => $this->router->generate('app_account_edit')
                    ]
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