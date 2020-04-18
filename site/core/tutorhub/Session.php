<?php

namespace TutorHub;


class Session
{
    /**
     * @var User
     */
    private static $user = null;

    /**
     * To be called at the top of each page that uses sessions.
     */
    public static function start()
    {
        session_register_shutdown();
        ini_set('session.cookie_httponly', 1);
        session_start();
    }

    /**
     * @return bool True if the user is logged in, false otherwise.
     */
    public static function isLogged() : bool
    {
        return self::getUser() !== null;
    }

    /**
     * Gets the logged in user.
     * The user is lazily fetched from the database.
     * Multiple invocations in the same session will only fetch once.
     *
     * @return User|null User object, or null if not logged in.
     */
    public static function getUser()
    {
        if (!isset($_SESSION['uid']))
            return null;
        if (self::$user === null)
            self::$user = User::fromKey(getDb(), $_SESSION['uid']);
        return self::$user;
    }

    /**
     * Sets the logged in user.
     *
     * @param User $user
     */
    public static function setUser(User $user)
    {
        self::$user = $user;
        $_SESSION['uid'] = $user->getKey();
    }

    /**
     * Invalidates this session, logging the user out.
     */
    public static function invalidate()
    {
        self::$user = null;
        unset($_SESSION['uid']);
    }
}