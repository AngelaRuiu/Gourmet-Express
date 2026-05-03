<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Core\SessionManager;
use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index(Request $_req, Response $response): never
    {
        $this->view($response, 'admin/dashboard.php', [
            'user'  => SessionManager::getUser(),
            'title' => 'Dashboard',
        ]);
    }
}