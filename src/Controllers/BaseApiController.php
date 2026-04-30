<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

/**
 * BaseApiController provides common functionality for all API controllers, such as standardized JSON responses and error handling.
 * 
 * All API controllers should extend this class to ensure consistent response formats and centralized error handling.
 * 
 * Usage:
 *   class MenuApiController extends BaseApiController
 *   {
 *       public function index(Request $request, Response $response): never
 *       {
 *           $dishes = (new MenuManager())->findAll();
 *           $response->success($dishes);
 *       }
 *   }
 */

abstract class BaseApiController
{
     /**
     * Validate required fields from the request body.
     * Returns the validated data array on success.
     * Calls $response->error() and exits on failure — controller never continues.
     *
     * Usage:
     *   $data = $this->validate($request, $response, ['name', 'price', 'category_id']);
     */
    protected function validate( Request  $request, Response $response,array $required): array {
        $data   = $request->all();
        $errors = [];

        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                $errors[] = "Field '{$field}' is required.";
            }
        }

        if (!empty($errors)) {
            $response->error(implode(' ', $errors), 422);
        }

        return $data;
    }

    // Helper method: return only specific keys from the request, already validated.
    protected function only(Request $request, array $keys): array
    {
        return $request->only($keys);
    }

    // Helper method: abort the request with an error response if a condition is not met (e.g. authorization check).
    protected function abortUnless(bool $condition, Response $response, string $message = 'Unauthorized'): void
    {
        if (!$condition) {
            $response->unauthorized($message);
        }
    }

}