<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Managers\MenuManager;

class MenuController extends BaseController
{
    // GET /menu - public page showing all menu items
    public function index(Request $_req, Response $response): never
    {
        $menu     = new MenuManager();
        $dishes   = $menu->findAll('category_id ASC');

        $this->view($response, 'pages/menu.php', [
            'title'  => 'Our Menu',
            'dishes' => $dishes,
        ]);
    }
}