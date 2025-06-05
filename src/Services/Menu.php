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

    public readonly array $menuDashboard;

    public readonly array $menuCategoryList;

    public readonly array $menuCategoryCreate;

    public readonly array $menuObjectList;

    public readonly array $menuObjectCreate;

    public readonly array $menuUserList;

    public readonly array $menuUserCreate;

    public readonly array $menuAccount;

    public readonly array $menuSignOut;

    public function __construct(UrlGeneratorInterface $router, Security $security)
    {
        $this->router = $router;

        $this->security = $security;

        $this->menuDashboard = [
            "title" => "Summary data",
            "icon" => "chart-simple",
            "url" => [
                "get" => $this->router->generate("app_dashboard"),
            ],
            "active" => true,
            "confirm" => false,
            "component" => "CDashboard",
            "id" => "ds1-$#",
        ];

        $this->menuCategoryList = [
            "title" => "Categories list",
            "sectionTitle" => "List of available categories",
            "icon" => "list",
            "active" => false,
            "confirm" => false,
            "component" => "CTable",
            "url" => [
                "get" => $this->router->generate("app_categories_list"),
                "post" => $this->router->generate("app_categories_edit"),
                "delete" => $this->router->generate("app_categories_delete"),
            ],
            "id" => "ds2-$#",
        ];

        $this->menuCategoryCreate = [
            "title" => "Add category",
            "sectionTitle" => "Create a new category",
            "icon" => "plus",
            "active" => false,
            "confirm" => false,
            "component" => "CForm",
            "url" => [
                "get" => $this->router->generate('app_categories_list'),
                "put" => $this->router->generate('app_categories_create')
            ],
            "id" => "ds3-$#",
        ];

        $this->menuObjectList = [
            "title" => "Data objects list",
            "sectionTitle" => "The Metropolitan Museum of Art Collection",
            "icon" => "chart-column",
            "active" => false,
            "confirm" => false,
            "component" => "CTable",
            "url" => [
                "get" => $this->router->generate('app_objectdata_list'),
                "put" => $this->router->generate('app_objectdata_create'),
                "delete" => $this->router->generate('app_objectdata_delete')
            ],
            "id" => "ds4-$#",
        ];

        $this->menuObjectCreate = [
            "title" => "Add a data object",
            "sectionTitle" => "Create a new data object",
            "icon" => "plus",
            "active" => false,
            "confirm" => false,
            "component" => "CForm",
            "url" => [
                "get" => $this->router->generate('app_objectdata_list'),
                "put" => $this->router->generate('app_objectdata_create')
            ],
            "id" => "ds5-$#",
        ];

        $this->menuUserList = [
            "title" => "Users list",
            "sectionTitle" => "Users list",
            "icon" => "users",
            "active" => false,
            "confirm" => false,
            "component" => "CTable",
            "url" => [
                "get" => $this->router->generate("app_users_list"),
                "post" => $this->router->generate("app_users_edit"),
                "delete" => $this->router->generate("app_users_delete"),
            ],
            "id" => "ds6-$#",
        ];

        $this->menuUserCreate = [
            "title" => "Add user",
            "sectionTitle" => "Create a new user",
            "icon" => "plus",
            "active" => false,
            "confirm" => false,
            "component" => "CForm",
            "url" => [
                "get" => $this->router->generate('app_users_list'),
                "put" => $this->router->generate('app_users_create')
            ],
            "id" => "ds7-$#",
        ];

        $this->menuAccount = [
            "title" => "Edit profile",
            "sectionTitle" => "Edit your personal data",
            "icon" => "user",
            "active" => false,
            "confirm" => false,
            "component" => "CForm",
            "url" => [
                "get" => $this->router->generate('app_account_list'),
                "put" => $this->router->generate('app_account_edit')
            ],
            "id" => "ds8-$#",
        ];

        $this->menuSignOut = [
            "title" => "Sign out",
            "icon" => "power-off",
            "route" => $this->router->generate("app_logout"),
            "confirm" => true,
            "id" => "ds9-$#",
        ];
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
                    $this->menuDashboard,
                ]
            ];

            $menusList[] = [
                "title" => "Categories",
                "icon" => "list",
                "children" => [
                    $this->menuCategoryList,
                    $this->menuCategoryCreate,
                ]
            ];
        }

        $menusList[] = [
            "title" => "Data objects",
            "icon" => "DataAnalysis",
            "children" => [
                $this->menuObjectList,
                $this->menuObjectCreate,
            ]
        ];

        if ($this->security->getUser()->isAdmin()) {
            $menusList[] = [
                "title" => "Users",
                "icon" => "User",
                "children" => [
                    $this->menuUserList,
                    $this->menuUserCreate,
                ]
            ];
        }

        $menusList[] = [
            "title" => "Account",
            "icon" => "Setting",
            "children" => [
                $this->menuAccount,
                $this->menuSignOut,
            ]
        ];

        return $menusList;
    }
}