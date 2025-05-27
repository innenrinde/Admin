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
                        "url" => [
                            "get" => $this->router->generate("app_dashboard"),
                        ],
                        "active" => true,
                        "confirm" => false,
                        "component" => "CDashboard"
                    ],
                ]
            ];

            $menusList[] = [
                "title" => "Categories",
                "icon" => "list",
                "children" => [
                    [
                        "title" => "Categories list",
                        "sectionTitle" => "List of available categories",
                        "icon" => "list",
//                        "route" => $this->router->generate("app_categories"),
                        "active" => false,
                        "confirm" => false,
                        "component" => "CTable",
                        "url" => [
                            "get" => $this->router->generate("app_categories_list"),
                            "post" => $this->router->generate("app_categories_edit"),
                            "delete" => $this->router->generate("app_categories_delete"),
                        ],
                    ],
                    [
                        "title" => "Add category",
                        "sectionTitle" => "Create a new category",
                        "icon" => "plus",
//                        "route" => $this->router->generate("app_categories_add"),
                        "active" => false,
                        "confirm" => false,
                        "component" => "CForm",
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
                    "sectionTitle" => "The Metropolitan Museum of Art Collection",
                    "icon" => "chart-column",
//                    "route" => $this->router->generate("app_objectdata"),
                    "active" => false,
                    "confirm" => false,
                    "component" => "CTable",
                    "url" => [
                        "get" => $this->router->generate('app_objectdata_list'),
                        "put" => $this->router->generate('app_objectdata_create'),
                        "delete" => $this->router->generate('app_objectdata_delete')
                    ]
                ],
                [
                    "title" => "Add a data object",
                    "sectionTitle" => "Create a new data object",
                    "icon" => "plus",
//                    "route" => $this->router->generate("app_objectdata_add"),
                    "active" => false,
                    "confirm" => false,
                    "component" => "CForm",
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
                        "sectionTitle" => "Users list",
                        "icon" => "users",
//                        "route" => $this->router->generate("app_users"),
                        "active" => false,
                        "confirm" => false,
                        "component" => "CTable",
                        "url" => [
                            "get" => $this->router->generate("app_users_list"),
                            "post" => $this->router->generate("app_users_edit"),
                            "delete" => $this->router->generate("app_users_delete"),
                        ],
                    ],
                    [
                        "title" => "Add user",
                        "sectionTitle" => "Create a new user",
                        "icon" => "plus",
//                        "route" => $this->router->generate("app_users_add"),
                        "active" => false,
                        "confirm" => false,
                        "component" => "CForm",
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
                    "sectionTitle" => "Edit your personal data",
                    "icon" => "user",
//                    "route" => $this->router->generate("app_account"),
                    "active" => false,
                    "confirm" => false,
                    "component" => "CForm",
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