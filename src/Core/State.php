<?php

namespace App\Core;

class State extends \Exception
{
    /**
     * @param string $message
     * @return State
     */
    public static function notFound(string $message): State
    {
        return new self($message, 404);
    }

    /**
     * @param $message
     * @return State
     */
    public static function badRequest(string $message): State
    {
        return new self($message, 400);
    }

    /**
     * @param $message
     * @return State
     */
    public static function forbidden(string $message): State
    {
        return new self($message, 403);
    }

    /**
     * @param \Exception|string $error
     * @return State
     */
    public static function error($error): State
    {
        if ($error instanceof \Exception) {
            return new self($error->getMessage(), 500, $error);
        } else {
            return new self($error, 500);
        }
    }

    /**
     * @param string $print
     * @param int $code
     * @return State
     */
    public static function quit(string $print = "", $code = 200): State
    {
        return new self($print, $code);
    }
}
