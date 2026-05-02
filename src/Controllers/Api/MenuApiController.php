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

    public function index(Request $request, Response $response): never
    {
        $items = $this->menu->findAll('category_id ASC');
        $response->success($items, 'Menu loaded successfully.');
    }

    // GET /api/v1/menu/{id} - for admin use only (not implemented yet)
    public function show(Request $request, Response $response): never
    {
        $id   = (int) $request->param('id');
        $item = $this->menu->findById($id);

        if (!$item) {
            $response->notFound("Menu item #{$id} not found.");
        }

        $response->success($item);
    }

    // POST /api/v1/menu - for admin use only (not implemented yet)
    public function store(Request $request, Response $response): never
    {
        $data = $this->validate($request, $response, ['name', 'price', 'category_id']);
        $safe = $this->only($request, ['name', 'description', 'price', 'category_id', 'is_available']);
        $id   = $this->menu->create($safe);

        $response->created(
            ['id' => $id, ...$safe],
            'Menu item created.'
        );
    }

    // PUT /api/v1/menu/{id} - for admin use only (not implemented yet)
    public function update(Request $request, Response $response): never
    {
        $id   = (int) $request->param('id');
        $safe = $this->only($request, ['name', 'description', 'price', 'category_id', 'is_available']);

        $this->menu->updateById($id, $safe);
        $response->success(null, 'Menu item updated.');
    }

    // DELETE /api/v1/menu/{id} - for admin use only (not implemented yet)
    public function destroy(Request $request, Response $response): never
    {
        $id = (int) $request->param('id');
        $this->menu->deleteById($id);
        $response->noContent();
    }
}