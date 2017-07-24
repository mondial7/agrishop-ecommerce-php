<?php

/**
 * @todo Secure cookie with https and httponly parameters
 */
class Cookie {


    /**
     * Add new cookie variable
     *
     * @param mixed key
     * @param mixed value
     * @param int days (optional)
     * @param string path (optional)
     * @return string cookie token
     */
    public static function add($key, $value, $days = 90){

        setcookie($key, $value, time() + (86400 * $days), "/", NULL, isset($_SERVER["HTTPS"]), true);

        return $value;

    }

    /**
     * Delete cookie
     *
     * @param string key
     * @return boolean
     */
    public static function remove($key){

        return setcookie($key, "", time() - 3600, "/", "httponly");

    }

    /**
     * Key exists
     *
     * @param string key
     * @return boolean
     */
    public static function exists($key){

        return isset($_COOKIE[$key]);

    }

    /**
     * Get cookie variable
     *
     * @param string key
     * @return mixed
     */
    public static function get($key){

        // Sintax valid since Php > 7.0
        // use
        // isset($_SESSION[$key]) ? $_SESSION[$key] : null
        // for previous versions
        return $_COOKIE[$key] ?? null;

    }

}
