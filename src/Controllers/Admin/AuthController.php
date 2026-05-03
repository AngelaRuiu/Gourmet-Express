<?php

namespace App\Controllers\Admin;

use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\SessionManager;
use App\Services\AuthService;
use App\Controllers\BaseController;

class AuthController extends BaseController
{
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService();
    }

    // GET /login 
    public function showLogin(Request $_req, Response $response): never
    {
        // Already logged in, send to admin
        if ($this->auth->isLoggedIn()) {
            $response->redirect('/dashboard');
        }

        $this->view($response, 'admin/login.php', [
            'error'   => SessionManager::getFlash('auth_error'),
            'success' => SessionManager::getFlash('auth_success'),
        ]);
    }

    // POST /login 
    public function login(Request $_req, Response $response): never
    {
        $username = trim($_req->input('username', ''));
        $password = $_req->input('password', '');

        if (empty($username) || empty($password)) {
            SessionManager::flash('auth_error', 'Username and password are required.');
            $response->redirect('/login');
        }

        $user = $this->auth->attempt($username, $password);

        if (!$user) {
            SessionManager::flash('auth_error', 'Invalid username or password.');
            $response->redirect('/login');
        }

        // Redirect to originally intended URL or default to /dashboard
       $default  = Config::get('app.admin_url') . '/dashboard';
       $intended = SessionManager::getFlash('intended_url') ?? $default;
       $response->redirect($intended);
    }

    // POST /logout
    public function logout(Request $_req, Response $response): never
    {
        $this->auth->logout();
        SessionManager::flash('auth_success', 'You have been logged out.');
        $response->redirect('/login');
    }
}