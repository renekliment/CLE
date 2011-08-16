<?php
/**
 * File containing some SEO functions
 *
 * @package CL
 * @author Rene Kliment <rene.kliment@gmail.com>
 * @version 1.1
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License - Version 3, 19 November 2007
 *
 * This software is dual-licensed - it is also available under a commercial license,
 * so there's possibility to use this software or its parts in software which is not license-compatible.
 * For more information about licensing, contact the author.
 */

/**
 * Class for SEO
 *
 * @package CL
 */
class CL_SEO
{
    /**
     * @var array some URL requested? let's cut it in pieces
     */
    public $URLArray = array();

    /**
     * @var integer sizeof($this->URLArray)
     */
    public $URLArraySize = 0;

    /**
     * @var object SimpleXML object representing file handler
     */
    protected $fileHandler;

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
     * Let's begin some stuff
     */
    function __construct()
    {
        /* Wanna some routing? */
        if (isset($_GET['seo'])) {
            $seo = trim($_GET['seo']);

            if (substr($seo, -1) == '/') {
                $seo = substr($seo, 0, -1);
            }

            $this->URLArray = explode('/', $seo);
            $this->URLArraySize = count($this->URLArray);
        }
    }

    /**
     * Returns file to include according to given URL params
     *
     * @global integer $id main and in all script well-known variable containing ID of some item
     * @return string filename
     */
    function getFileToInclude()
    {
        global $_id;

        if ($this->URLArraySize === 0) {
            $ret = CL::getConf('CL_SEO/defaultFile');
        } else {
            $try = $this->lookUpInXMLFile();
            if ($try) {
                $ret = $try;
            } else {
                header('HTTP/1.0 404 Not Found');
                exit;
            }
        }
        
        if (!isset($_id)) {
            $_id = 0;
        }

        return $ret;
    }

    /**
     * Goes through section in conf file and finds required filename
     * This function is a recursion one - it goes also through subsections.
     *
     * @param object $handler file(first level) / section (following levels) handler
     * @param int $i iterator
     * @global integer $_id see documentation - global variables
     * @global string $_stringId see documentation - global variables
     * @return string|bool filename to include or FALSE
     */
    function checkSection($handler, $i)
    {
        global $_id, $_stringId;

        foreach ($handler->section as $section) {

            if ($section['name'] == $this->URLArray[$i]) {
                foreach ($section->item as $item) {
                    $ok = TRUE;
                    if (isset($item['mask'])) {
                        $arr = explode(' ', (string)$item['mask']);

                        if ($this->URLArraySize != count($arr)+1+$i) {
                            $ok = FALSE; 
                        } else {
                            foreach ($arr as $n => $value) {
                                if ($this->URLArray[$n+$i+1] != $value
                                    AND $value != '*'
                                    AND $value != '$'
                                    AND $value != '$$'
                                ) {
                                    $ok = FALSE;
                                } elseif ($value == '$') {
                                    $_id = (int)$this->URLArray[$n+$i+1];
                                } elseif ($value == '$$') {
                                    $_stringId = $this->URLArray[$n+$i+1];
                                }
                            }

                            if ($ok == FALSE) {
                                $_id = 0;
                                $_stringId = '';
                            }
                        }
                    } else {
                        if ($this->URLArraySize != (1 + $i)) {
                            $ok = FALSE;
                        }
                    }

                    if ($ok) {
                        if (!isset($_id) OR !$_id) {
                            $_id = 0;
                            if (isset($_GET['id']) AND $_GET['id']) {
                                $_id = (int)$_GET['id'];
                            }
                        }

                        return $item;
                    }

                }

                return $this->checkSection($section, $i+1);

                break;
                return FALSE;
            }
            
        }
    }

    /**
     * Obtain filename from configuration file, if there is any match with parameters in given URL
     *
     * @return string|bool filename or FALSE
     */
    function lookUpInXMLFile()
    {
        if (!$this->fileHandler) {
            $this->fileHandler = simplexml_load_file(CL::getConf('CL_SEO/fileName'));
        }

        return $this->checkSection($this->fileHandler, 0);
    }
    
    /**
     * Returns nice SEO string (including only a-z, A-Z, 0-9,-,_)
     *
     * @copyright Jakub Vrána, http://php.vrana.cz/
     * @param string $string input string
     * @return string transformed string
     */
    function friendlyURL($string)
    {
        $url = $string;
        $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
        $url = trim($url, "-");
        $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
        $url = strtolower($url);
        $url = preg_replace('~[^-a-z0-9_]+~', '', $url);

        return $url;
    }

}
?>