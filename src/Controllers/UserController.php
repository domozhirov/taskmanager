<?php

namespace App\Controllers;

use App\Core\AbstractController;
use App\Core\State;
use App\Core\User;
use App\Exceptions\User\UserException;

class UserController extends AbstractController
{
    /**
     * @param string $login
     * @param string $password
     * @return array
     * @throws State
     */
    public function loginAction(string $login, string $password)
    {
        try {
            $user = User::login($login, $password);
        } catch (UserException $e) {
            throw State::notFound('User not found');
        }

        return $user->toArray();
    }

    /**
     * @return array
     */
    public function logoutAction()
    {
        User::logout();

        return [
            'logout' => true,
        ];
    }
}
