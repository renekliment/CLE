<?php
/**
 * File containing class for some basic formatting
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
 * Class for some basic formatting
 * @package CL
 */
class CL_Xety
{
    /**
     * @var object the instance of the class (Singleton pattern)
     */
    private static $instance;

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
     * Does just convert of \n to <br />
     *
     * @param string $text input text
     * @return string formatted text
     */
    function basic($text)
    {
        return nl2br(htmlspecialchars($text, ENT_QUOTES));
    }

    /**
     * Returns plain text without any formatting
     *
     * @param string $text input text
     * @return string formatted text
     */
    function plain($text)
    {
        return htmlspecialchars($text, ENT_QUOTES);
    }

    /**
     * Useful, if you wanna echo some price or something; it devides the given string with a space each 3 characters
     *
     * @param integer $num transforms this into string and devides it
     * @return string the divided string
     */
    function separateNums($num)
    {
        if (strlen($num) > 3) {
            $new_num = '';
            $strlen = ceil(strlen($num)/3);
            for ($i=0; $i<$strlen; $i++) {
                if (strlen($num) < 4) {
                    $new_num = $num.' '.$new_num;
                } else {
                    $blah = substr($num, -3);
                    $num = substr($num,0,-3);
                    $new_num = ' '.$blah.$new_num;
                }
            }
        } else {
            return $num;
        }

        return $new_num;
    }

    /**
     * This function delete spaces in given string a returns it back
     * Useful for some things to upload it into the database, like if the column is integer type and you wanna store some price, ICQ UIN, etc
     *
     * @param string $string string to delete spaces in
     * @return string string with deleteted spaces
     */
    function deleteSpaces($string)
    {
        return str_replace(' ','', $string);
    }
}

?>