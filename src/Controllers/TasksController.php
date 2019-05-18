<?php

namespace App\Controllers;

use App\Core\AbstractController;
use App\Core\State;
use App\Models\Task;

class TasksController extends AbstractController
{
    public const LIMIT = 3;

    /**
     * @param int $p
     * @return array
     * @throws State
     */
    public function listAction(int $p = 0): array
    {
        $total = Task::getTotal();

        if ($p < 0) {
            throw State::badRequest('Page parameter requires a positive number');
        }

        $tasks = $total ? Task::getAll($p * Task::LIMIT, Task::LIMIT, "id DESC, completed DESC") : [];

        if ($p && $total && !$tasks) {
            throw State::notFound('Not Found');
        }

        $pages = (int)ceil($total / Task::LIMIT);

        return [
            'page'  => $p,
            'pages' => $pages,
            'total' => $total,
            'tasks' => $tasks,
        ];
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $text
     * @return array
     * @throws State
     */
    public function addAction(string $name, string $email, string $text)
    {
        $request = $this->engine->getRequest();

        if ($request->getMethod() !== 'POST') {
            throw State::badRequest('Post request required');
        }

        $task = (new Task)
            ->setName($name)
            ->setEmail($email)
            ->setText($text);

        $task->add();

        return $task->toArray();
    }
}
