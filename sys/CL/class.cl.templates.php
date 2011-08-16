<?php
/**
 * File containing class of simple template system
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
 * This class is a simple template system
 * @package CL
 */
class CL_Templates
{
    /**
     * @var string the main content, everything is there
     */
    protected $content;

    /**
     * @var string the directory of the layout
     */
    protected static $directory;

    /**
     * @var string namespace for directory of module
     */
    protected $moduleNamespace = '';

    /**
     * @var array variables, which are gonna be deleted at getContent(), added there by add() function
     */
    protected $delVars = array();

    /**
     * @var array the cache for templates - there is no need to fopen file 20 times, is it?
     */
    protected static $cache = array();

    /**
     * @var object the instance of the class (Singleton pattern)
     */
    private static $instance;

    /**
     * To constructor, we can pass argument to declare which template to use as main (base)
     * It is also the only way, how to load main template (and it must be loaded)
     *
     * @param string $file_name name of the template file
     * @param string $part part of the file to load
     */
    function __construct($file_name='', $part='')
    {
        if (!$file_name) {
            $file_name = CL::getConf('CL_Templates/default_main_file');
        }
        
        $this->content = $this->getTpl($file_name, $part);
    }

    /**
     * Function to get the instance of the class
     *
     * @param string $file_name name of the template file
     * @param string $part part of the file to load
     * @return object instance of this class
     */
    public static function getInstance($file_name='', $part='')
    {
        if (self::$instance === NULL) {
            self::$instance = new self($file_name, $part);
        }

        return self::$instance;
    }

    /**
     * This function returns the path (according to the root of our website) of given file.
     * Example: "guestbook/form.tpl" -> "layouts/theBestWorldsTemplate/guestbook/form.tpl"
     *
     * @param string $file the file whom path we wanna know
     * @return string the file's path
     */
    function getFilePath($file)
    {
        if (substr($file, 0, 2) == '##') {
            $fileNew = CL::getConf('CL_Templates/dir').self::$directory.substr($file, 2);
            if (!file_exists($fileNew)) {
                $fileNew = CL::getConf('CL_Templates/dir').'default/'.substr($file, 2);
            }
            $file = $fileNew;
        } elseif (substr($file, 0, 1) == '#') {
            $file = substr($file, 1);
        } elseif (substr($file, 0, 8) == 'modules/' OR substr($this->moduleNamespace, 0, 8) == 'modules/') {
            $file = $this->moduleNamespace.$file;
        } else {
            $file = CL::getConf('CL_Templates/dir').self::$directory.'/'.$file;
        }

        if (file_exists($file)) {
            return $file;
        } else {
            return FALSE;
        }

    }

    /**
     * This function returns the HTML(or other) template from the given file
     *
     * @param string $file_name the file we wanna load
     * @param string $part non-required param, it specifies the part of the given file, if there is not set, the first will be selected
     * @return string the file (also part of it) we wanted
     */
    function getTpl($file_name, $part='main')
    {
        $file_name = $this->getFilePath($file_name);

        if (!in_array($file_name, self::$cache)) {
            $file_handle = fopen($file_name, 'r');
            $file_content = fread($file_handle, fileSize($file_name));
            fclose($file_handle);
            self::$cache[$file_name] = $file_content;
        } else {
            $file_content = self::$cache[$file_name];
        }

        $template = new SimpleXMLElement($file_content);
        if (!$part) {
            $part = $template['defaultPart'];
        }

        $result = $template->xpath("/template/part[@id='".strtolower($part)."']");
        return (substr($result[0], 0, 1) == "\n") ? substr($result[0], 1) : $result[0];
    }

    /**
     * This function sets the variable(s) in the template we loaded
     *
     * @param string|array $var if it is string, it is the name of variable to be set and will be set just one variable; if it is array, we can set more variables at the same time - params ex.: array("name_of_var_to_set" => "value_to_be_set", ...)
     * @param string $val the value to set (useful only if we wanna set just one variable at the one function call)
     */
    function set($var, $val='')
    {
        if (is_array($var)) {
            foreach ($var as $variable => $value) {
                if (!$variable) {
                    $variable = 'content';
                }
                $this->content = str_replace('${'.$variable.'}', $value, $this->content);
            }
        } else {
            if (func_num_args() == 1) {
                $val = $var;
                $var = 'content';
            }
            $this->content = str_replace('${'.$var.'}', $val, $this->content);
        }
    }

    /**
     * This function adds content the variable(s) in the template we loaded (exactly the same like set(), but this just adds content and some other content can be added later to the same variable)
     *
     * @param string|array $var if it is string, it is the name of variable to add content to; if it is array, we can add content to more variables at the same time - params ex.: array("name_of_var" => "value", ...)
     * @param string $val the value to add (useful only if we wanna add content to just one variable at the one function call)
     */
    function add($var, $val='')
    {
        if (is_array($var)) {
            foreach ($var as $variable => $value) {
                if (!$variable) {
                    $variable = 'content';
                }

                if (!in_array($variable, $this->delVars)) {
                    $this->delVars[] = $variable;
                }

                $this->content = str_replace('${'.$variable.'}', $value.'${'.$variable.'}', $this->content);
            }
        } else {
            if (func_num_args() == 1) {
                $val = $var;
                $var = 'content';
            }

            if (!in_array($var, $this->delVars)) {
                $this->delVars[] = $var;
            }

            $this->content = str_replace('${'.$var.'}', $val.'${'.$var.'}', $this->content);
        }
    }

    /**
     * This function sets the content of given variable(s) to '' - NULL, just deletes the ${name_of_var} string
     * it has unlimited number of parameters
     */
    function setNull()
    {
        for ($i=0; $i<func_num_args(); $i++) {
            $variable = func_get_arg($i);
            $this->content = str_replace('${'.$variable.'}', '', $this->content);
        }
    }

    /**
     * This function returns the directory, which is template in
     *
     * @return string the directory of template
     */
    function getDirectory()
    {
        return self::$directory;
    }

    /**
     * Sets the "workspace" directory - directory, where the proper
     * templates are located.
     *
     * @param string $directory directory
     */
    static function setDirectory($directory)
    {
        self::$directory = $directory;
    }

    /**
     * Cancels directory namespace previously set by $this->setNamespace()
     */
    function cancelNamespace()
    {
        $this->moduleNamespace = '';
    }

    /**
     * Sets directory namespace, so there is no more need to write
     * for example "modules/nameOfMyModule/tpl/admin/add.tpl",
     * but just "admin/add.tpl" or "add.tpl" as $this->getTpl() parameter.
     *
     * @param string $directory directory, which is gonna be used as namespace
     */
    function setNamespace($directory)
    {
        $this->moduleNamespace = $directory;
    }

    /**
     * This function gets the whole content of the template
     * Also sets needed lang constants and deletes the variables, which were set to be deleted
     *
     * @param bool $langReplace replace {LNG_*} by the proper constants?
     * @return string the whole content of the template
     */
    function getContent($langReplace=TRUE)
    {
        for ($i=0; $i<sizeof($this->delVars); $i++) {
            $this->content = str_replace('${'.$this->delVars[$i].'}', '', $this->content);
        }

        if ($langReplace) {
            preg_match_all('/\{(LNG_)(.*?)\}/si',$this->content, $regs);

            foreach ($regs[2] as $con) {
                $this->content = str_replace('{LNG_'.(string)$con.'}', constant('LNG_'.$con), $this->content);
            }
        }

        $this->content = str_replace('${content}', '', $this->content);

        return $this->content;
    }
}

?>