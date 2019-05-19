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
     * @param string $sort_by
     * @return array
     * @throws State
     */
    public function listAction(int $p = 0, string $sort_by = ''): array
    {
        $total = Task::getTotal();

        if ($p < 0) {
            throw State::badRequest('Page parameter requires a positive number');
        }

        $tasks = $total ? Task::getAll($p * Task::LIMIT, Task::LIMIT, $sort_by) : [];

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

        if ($user = $this->engine->getUser()) {
            $name = $user->getName();
            $email = $user->getEmail();
        }

        $task = (new Task)
            ->setName($name)
            ->setEmail($email)
            ->setText($text);

        $task->add();

        return $task->toArray();
    }

    /**
     * @param int $id
     * @param int $status
     * @return array
     * @throws State
     */
    public function changeStatusAction(int $id, int $status): array
    {
        if ($task = Task::getById($id)) {
            $task->setStatus($status)->update();
        } else {
            throw State::notFound("Task not found by id '$id'");
        }

        return $task->toArray();
    }

    /**
     * @param int $id
     * @param string $text
     * @return array
     * @throws State
     */
    public function changeTextAction(int $id, string $text): array
    {
        if (!$text) {
            throw State::badRequest('Param text is empty');
        }

        if ($task = Task::getById($id)) {
            $task->setText($text)->update();
        } else {
            throw State::notFound("Task not found by id '$id'");
        }

        return $task->toArray();
    }

    /**
     * @param int $id
     * @return array
     * @throws State
     */
    public function getAction(int $id): ?array
    {
        if (!$task = Task::getById($id)) {
            throw State::notFound("Task not found by id '$id'");
        }

        return $task->toArray();
    }
}
