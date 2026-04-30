<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApiController;
use App\Core\Request;
use App\Core\Response;
use App\Managers\MenuManager;

class MenuApiController extends BaseApiController
{
    private MenuManager $menu;

    public function __construct()
    {
        $this->menu = new MenuManager();
    }

    /** GET /api/v1/menu */
    public function index(Request $request, Response $response): never
    {
        $items = $this->menu->findAll('category_id ASC');
        $response->success($items);
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
        $id   = $this->menu->create($this->only($request, ['name', 'description', 'price', 'category_id']));
        $response->created(['id' => $id], 'Menu item created.');
    }

    // PUT /api/v1/menu/{id} - for admin use only (not implemented yet)
    public function update(Request $request, Response $response): never
    {
        $id   = (int) $request->param('id');
        $data = $this->only($request, ['name', 'description', 'price', 'category_id']);
        $this->menu->updateById($id, $data);
        $response->success(null, 'Menu item updated.');
    }

    // DELETE /api/v1/menu/{id} - for admin use only(not implemented yet)
    public function destroy(Request $request, Response $response): never
    {
        $id = (int) $request->param('id');
        $this->menu->deleteById($id);
        $response->noContent();
    }
}