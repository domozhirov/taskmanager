<?php

namespace App\Core;

use App\Exceptions\User\LoginIncorrectException;
use App\Exceptions\User\PasswordIncorrectException;
use App\Exceptions\User\UserException;

class User
{
    /**
     * @var string
     */
    protected static $login;
    /**
     * @var string
     */
    protected static $access;

    public static function getCurrent(): ?\App\Models\User
    {
        $login  = static::getLogin();
        $access = static::getAccess();

        if ($login && $access) {
            $user = \App\Models\User::get($login);

            if ($access === static::getHash($user->getId(), $user->getEmail(), $user->getPassword())) {
                return $user;
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public static function getLogin(): ?string
    {
        if (is_null(self::$login)) {
            self::$login = isset($_COOKIE['login']) ? htmlspecialchars(trim($_COOKIE['login'])) : null;
        }

        return self::$login;
    }

    /**
     * @return string
     */
    public static function getAccess(): ?string
    {
        if (is_null(self::$access)) {
            self::$access = isset($_COOKIE['access']) ? trim($_COOKIE['access']) : null;
        }

        return self::$access;
    }

    /**
     * @param string $login
     * @param string $password
     * @param bool $remember
     * @return \App\Models\User
     * @throws UserException
     */
    public static function login(string $login, string $password, bool $remember = true)
    {
        $user = \App\Models\User::get($login);

        if ($user->getLogin() != $login) {
            throw new LoginIncorrectException();
        }

        if ($user->getPassword() != $password) {
            throw new PasswordIncorrectException();
        }

        $hash = static::getHash($user->getId(), $user->getEmail(), $password);

        static::setCookie($login, $hash, $remember ? time() + 60 * 60 * 24 * 14 : 0);

        return $user;
    }

    /**
     * @return void
     */
    public static function logout(): void
    {
        static::removeCookie();
    }

    /**
     * @param int $user_id
     * @param string $email
     * @param string $password
     * @return string
     */
    protected static function getHash(int $user_id, string $email, string $password): string
    {
        return md5("vsw[ffeq" . $user_id . $email . $password . $_SERVER['HTTP_USER_AGENT'] . "AdscaWW");
    }


    /**
     * @param string $login
     * @param string $access
     * @param int $expire
     * @return void
     */
    protected static function setCookie($login, $access, $expire = 0): void
    {
        setcookie("login", $login, $expire, "/");
        setcookie("access", $access, $expire, "/");
    }

    /**
     * @return void
     */
    protected static function removeCookie(): void
    {
        setcookie("login", '', -1000, "/");
        setcookie("access", '', -1000, "/");
    }
}
