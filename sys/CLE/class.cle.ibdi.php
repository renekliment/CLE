<?php
/**
 * File containing some database (dibi) related functions
 *
 * @package CLE
 * @subpackage Classes
 * @author Rene Kliment <rene.kliment@gmail.com>
 * @version 1.0
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License - Version 3, 19 November 2007
 *
 * This software is dual-licensed - it is also available under a commercial license,
 * so there's possibility to use this software or its parts in software which is not license-compatible.
 * For more information about licensing, contact the author.
 */

/**
 * Class, which includes database (dibi) related functions
 *
 * @package CLE
 * @subpackage Classes
 */
class CLE_Ibdi
{
    /**
     * @var object the instance of the class (Singleton pattern)
     */
    private static $instance;

    /**
     * @var bool active connection?
     */
    private $connected = FALSE;

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
     * Makes connection to DB via dibi
     *
     * @global obejct $_dibi db object
     * @return boolean are we connected to the database?
     */
    function connect()
    {
        global $_dibi;

        try {
            $_dibi = new DibiConnection(array(
                'driver'   => CL::getConf('CLE_Ibdi/driver'),
                'host'     => CL::getConf('CLE_Ibdi/server'),
                'username' => CL::getConf('CLE_Ibdi/user'),
                'password' => CL::getConf('CLE_Ibdi/password'),
                'database' => CL::getConf('CLE_Ibdi/database'),
                'charset'  => 'utf8'
            ));
        
            $this->connected = TRUE;
        } catch (DibiException $e) {
            $this->connected = FALSE;
        }

        return $this->connected;
    }

    /**
     * Returns $this->connected variable
     * @return bool $this->connected
     */
    function isConnected()
    {
        return $this->connected;
    }

    /**
     * Gets the full table name according to the table label and prefix
     * (everything according to the .xml config file)
     *
     * @param string $table name of DB table
     * @return string the table full name
     */
    static function getNameOfTable($table)
    {
        return CL::getConf('CLE_Ibdi/prefix').$table;
    }

    /**
     * Returns number of rows in table
     *
     * @global object $_dibi database object
     * @param string $table table name
     * @return integer number of rows in given db table
     */
    function rowsTable($table)
    {
        global $_dibi;

        return count($_dibi->query("SELECT `id` FROM `".self::getNameOfTable($table)."`"));
    }

    /**
     * Return wanted value according to given ID
     * for example: I have user's ID and I want his name:
     * ->id2everything(5, 'users'); (default column is name, so if we want name, there is no need of specifying it)
     *
     * @global object $_dibi database object
     * @param integer $id id value
     * @param string $table database table (there must be this column)
     * @param string $what column you want
     * @return string wanted value
     */
    function id2everything($id, $table, $what='name')
    {
        global $_dibi;

        return $_dibi->fetchSingle("SELECT `$what` FROM `".self::getNameOfTable($table)."` WHERE `id`=%i",(int)$id);
    }

    /**
     * Finds out, if there is a row in table with given ID
     *
     * @global object $_dibi database object
     * @param integer $id row ID
     * @param string $table database table
     * @return bool exists, or not?
     */
    function rowExists($id, $table)
    {
        global $_dibi;

        return (bool)count($_dibi->query("SELECT `id` FROM `".self::getNameOfTable($table)."` WHERE `id`=%i", (int)$id));
    }

    /**
     * Deletes a row in given table with given ID
     *
     * @global object $_dibi database object
     * @param integer $id ID of row
     * @param string $table database table
     * @return bool deleted, or not?
     */
    function delete($id, $table)
    {
        global $_dibi;

        return (bool)$_dibi->query("DELETE FROM `".self::getNameOfTable($table)."` WHERE `id`=%i", (int)$id);
    }

    /**
     * Returns minmal and maximal value of items in a column;
     *
     * Can be used for example on a table of books
     * to get the cheapest and the most expensive one
     *
     * @global object $_dibi database object
     * @param string $col column name
     * @param string $table database table
     * @return array array(minimal value, maximal value)
     */
    function minMax($col, $table)
    {
        global $_dibi;
        
        $min = $_dibi->fetchSingle("SELECT MIN(".$col.")
                                    FROM `".self::getNameOfTable($table)."`"
        );


        $max = $_dibi->fetchSingle("SELECT MAX(".$col.")
                                    FROM `".self::getNameOfTable($table)."`"
        );

        return array($min, $max);
    }

}

?>