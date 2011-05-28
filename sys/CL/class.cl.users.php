<?php
/**
 * File containing class for manipulation with users
 *
 * @package CL
 * @author Rene Kliment <rene.kliment@gmail.com>
 * @version 1.0
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License - Version 3, 19 November 2007
 *
 * This software is dual-licensed - it is also available under a commercial license,
 * so there's possibility to use this software or its parts in software which is not license-compatible.
 * For more information about licensing, contact the author.
 */

/**
 * Class which provides methods for logging, verifying, ..., users.
 * It is just abstract, because it is kind of (case of use)-depended thing,
 * but the there is some universal interface, which is actually this class.
 * @package CL
 */
abstract class CL_Users
{
    /**
     * @var object the instance of the class (Singleton pattern)
     */
    private static $instance;

    /**
     * Sets the counter of logins, if it not set already
     */
    function __construct()
    {
        if (!isset($_SESSION['login_counter'])) {
            $_SESSION['login_counter'] = 0;
        }
    }

    /**
     * Function to get the instance of the class
     *
     * @return object instance of this class
     */
    public static function getInstance()
    {
        if (self::$instance === NULL) {
            self::$instance = new self;
        }
        
        return self::$instance;
    }

    /**
     * Finds out, if the user is logged in
     *
     * @return bool if user is logged in, returns TRUE, else FALSE
     */
    function isLoggedIn()
    {
        if (isset($_SESSION['logged']) AND $_SESSION['logged']==TRUE) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Sets the user as logged in
     *
     * @return bool if setting was successful, returns TRUE, else FALSE
     */
    function setLoggedIn()
    {
        $_SESSION['logged'] = TRUE;
    }

    /**
     * Returns ID of logged user
     *
     * @return integer user ID
     */
    function loggedUserID()
    {
        return @$_SESSION['id'];
    }

    /**
     * Return hashed password
     *
     * @param string $pass password
     * @param string $salt salt (he use higher security :-P)
     * @return string hashed password
     */
    function password($pass, $salt)
    {
        return hash_hmac('sha1', sha1($pass), $salt);
    }

    /**
     * Verifies the user - needs to be defined in concrete case of use, because it depends on many things
     *
     * @param $id user id
     * @param $pass user password
     */
    abstract function logIn($id, $pass);

    /**
     * Changes the password of the user - needs to be defined in concrete case of use, because it depends on many things
     *
     * @param $id user id
     * @param $newpass new password of the user
     */
    abstract function changePass($id, $newpass);

    /**
     * Logs out the user - needs to be defined in concrete case of use, because it depends on many things
     */
    abstract function logOut();
}
?>