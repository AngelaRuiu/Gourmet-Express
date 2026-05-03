<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApiController;
use App\Core\Request;
use App\Core\Response;
use App\Managers\MenuManager;
use App\Constants\AppConstants;

class MenuApiController extends BaseApiController
{
    private MenuManager $menu;

    public function __construct()
    {
        $this->menu = new MenuManager();
    }

    public function index(Request $_req, Response $response): never
    {
        $items = $this->menu->findAll('category_id ASC');
        $response->success($items, 'Menu loaded successfully.');
    }

    // GET /api/v1/menu/{id} - for admin use only (not implemented yet)
    public function show(Request $_req, Response $response): never
    {
        $id   = (int) $_req->param('id');
        $item = $this->menu->findById($id);

        if (!$item) {
            $response->notFound("Menu item #{$id} not found.");
        }

        $response->success($item);
    }

    // POST /api/v1/menu - for admin use only (not implemented yet)
    public function store(Request $_req, Response $response): never
    {
        $data = $this->validate($_req, $response, ['name', 'price', 'category_id']);
        $safe = $this->only($_req, ['name', 'description', 'price', 'category_id', 'is_available']);
        $id   = $this->menu->create($safe);

        $response->created(
            ['id' => $id, ...$safe],
            'Menu item created.'
        );
    }

    // PUT /api/v1/menu/{id} - for admin use only (not implemented yet)
    public function update(Request $_req, Response $response): never
    {
        $id   = (int) $_req->param('id');
        $safe = $this->only($_req, ['name', 'description', 'price', 'category_id', 'is_available']);

        $this->menu->updateById($id, $safe);
        $response->success(null, 'Menu item updated.');
    }

    // DELETE /api/v1/menu/{id} - for admin use only (not implemented yet)
    public function destroy(Request $_req, Response $response): never
    {
        $id = (int) $_req->param('id');
        $this->menu->deleteById($id);
        $response->noContent();
    }
}