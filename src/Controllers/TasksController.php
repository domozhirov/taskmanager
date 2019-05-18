<?php

namespace App\Controllers;

use App\Core\AbstractController;

class TasksController extends AbstractController
{
    /**
     * @param int $p
     * @return array
     */
    public function listAction(int $p = 0): array
    {
        return [
            'bla' => 123,
        ];
    }
}
