<?php
/**
 * File containing functions for CMS modules
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
 * Class, which includes modules functions
 *
 * @package CLE
 * @subpackage Classes
 */
class CLE_Modules
{
    /**
     * @var object the instance of the class (Singleton pattern)
     */
    private static $instance;

    /**
     * @var array array of loaded modules
     */
    private $loadedModules = array();

    /**
     * @var array directories, where language files are located,
     * (should be loaded, when language is already set)
     */
    private $langDirectories = array();

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
     * Class constructor - autorun of modules, etc ...
     */
    function __construct()
    {
        CL::initConfHandler();
        $handler = CL::$configHandler;

        $result = $handler->xpath("/CL_config/CLE_Modules/module[@autorun='1'][@enabled='1'][@installed='1']");
        foreach ($result as $module) {
            $this->load($module);
        }
    }

    /**
     * Finds out, if given module is loaded
     *
     * @param string $module name (ID) of the module
     * @return bool is, or not?
     */
    function isLoaded($module)
    {
        return (bool)in_array($module, $this->loadedModules);
    }

    /**
     * Loads a module, if it is not loaded
     *
     * @param string $module name (ID) of the module
     * @return bool was it successful?
     */
    function load($module)
    {
        global $_autoloadArray, $_setVarsArray, $_addVarsArray;

        if (!$this->isLoaded($module)) {
            $langDir = './modules/'.$module.'/lang/';
            if (file_exists($langDir)) {
                $this->langDirectories[] = $langDir;
                if (isset($_SESSION['language']) AND $_SESSION['language']) {
                    $this->loadLangFiles($_SESSION['language']);
                }
            }

            $initFile = './modules/'.$module.'/init.php';
            if (file_exists($initFile)) {
                require_once $initFile;
            }

            $this->loadedModules[] = $module;
        }

        return TRUE;
    }

    /**
     * Let's load all language files!
     *
     * @param string $language language
     */
    function loadLangFiles($language)
    {
        foreach ($this->langDirectories as $dir) {
            if (substr($dir, 0, 8) == 'modules/' OR substr($dir, 0, 10) == './modules/') {
                preg_match('/(modules)\/(.*?)\/(lang)\//si', $dir, $output);

                require_once $dir.'/lang.'.$output[2].'.'.$language.'.php';
            }
        }

        unset($this->langDirectories);
        $this->langDirectories = array();
    }

}

?>