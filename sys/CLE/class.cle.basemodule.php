<?php
/**
 * File containing base class for not so trivial modules
 *
 * @package CLE
 * @subpackage Classes
 * @author Rene Kliment <rene.kliment@gmail.com>
 * @version 1.1
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License - Version 3, 19 November 2007
 *
 * This software is dual-licensed - it is also available under a commercial license,
 * so there's possibility to use this software or its parts in software which is not license-compatible.
 * For more information about licensing, contact the author.
 */

/**
 * Base class for not so trivial modules
 *
 * @package CLE
 * @subpackage Classes
 */
class CLE_BaseModule
{
    /**
     * @var string name of main DB table used by module
     */
    protected $dbTable;

    /**
     * Returns $this->dbTable
     *
     * @return string name of the table
     */
    function getDbTable()
    {
        return $this->dbTable;
    }

    /**
     * Finds out, if item in main module's DB table exists
     *
     * @param integer $id item ID
     * @return bool exists or not?
     */
    function exist($id)
    {
        return CLE_Ibdi::getInstance()->rowExists($id, $this->dbTable);
    }

    /**
     * Well-known id2column function, which returns any of the information
     * about some item by its ID. It uses main DB table of the module.
     * 
     * @param integer $id item ID
     * @param string $what needed column
     * @return integer|string wanted value
     */
    function id2column($id, $what='name')
    {
        return CLE_Ibdi::getInstance()->id2column($id, $this->dbTable, $what);
    }

    /**
     * Counts number of all entries in main DB of module
     *
     * @return integer number of entries
     */
    function total()
    {
        return CLE_Ibdi::getInstance()->rowsTable($this->dbTable);
    }

    /**
     * Returns minmal and maximal value of items in a column
     *
     * @param string $col column name
     * @return array array(minimal value, maximal value)
     */
    function minMax($col)
    {
        return CLE_Ibdi::getInstance()->minMax($col, $this->dbTable);
    }

    /**
     * Loads all columns (all information) of a row
     * according to a row ID column
     *
     * @param $id ID of a DB row
     * @return array of all columns related to the specified row
     */
    function load($id)
    {
        return CLE_Ibdi::getInstance()->load($id, $this->dbTable);
    }

    /**
     * Load rows from a DB table (limited and sorted by method parameters)
     *
     * @param string $start start record for limit
     * @param string $limit number of records requested (limit)
     * @param string $orderColumn column to order rows by
     * @param string $order order type
     * @return object db rows
     */
    function loadMany($start='', $limit='', $orderColumn='', $order='ASC')
    {
        return CLE_Ibdi::getInstance()->loadMany($this->dbTable, $start, $limit, $orderColumn, $order);
    }

}

?>