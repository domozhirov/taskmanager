<?php

namespace App\Controllers;

use App\Core\AbstractController;
use App\Models\Task;

class TasksController extends AbstractController
{
    /**
     * @param int $p
     * @return array
     */
    public function listAction(int $p = 0): array
    {
        $total = Task::getTotal();

        return [
            'total' => $total,
            'tasks' => $total ? Task::getAll($p * 3) : [],
        ];
    }
}
