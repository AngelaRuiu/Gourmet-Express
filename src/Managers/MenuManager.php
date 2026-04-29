<?php

namespace App\Managers;

use App\Core\BaseManager;
use App\Core\Database;

class MenuManager extends BaseManager
{

    protected function getTable(): string
    {
        return Database::TABLE_MENU_ITEMS;
    }

}