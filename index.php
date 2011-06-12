<?php
/**
 * @package CLE
 * @subpackage SysFiles
 * @author Rene Kliment <rene.kliment@gmail.com>
 * @version 1.0
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License - Version 3, 19 November 2007
 * 
 * This software is dual-licensed - it is also available under a commercial license,
 * so there's possibility to use this software or its parts in software which is not license-compatible.
 * For more information about licensing, contact the author.
 */

/* Including of the whole CL framework */
require_once './sys/CL/class.cl.php';
require_once './sys/CL/class.cl.users.php';
require_once './sys/CL/class.cl.templates.php';
require_once './sys/CL/class.cl.images.php';
require_once './sys/CL/class.cl.seo.php';
require_once './sys/CL/class.cl.xety.php';

/* Including of the CLE engine */
require_once './sys/CLE/class.cle.ibdi.php';
require_once './sys/CLE/class.cle.modules.php';
require_once './sys/CLE/class.cle.basemodule.php';

/* CL base object */
$_CL = CL::getInstance();

/* Some object and arrays related to (auto)loading of modules */
$_autoloadArray = array();
$_setVarsArray = array();
$_addVarsArray = array();

if (CL::getConf('CLE_Ibdi/enabled') == 1) {
    require_once './sys/external/dibi.min.php';

    if (!CLE_Ibdi::getInstance()->connect() AND CL::getConf('CLE_Ibdi/terminateScriptOnError') == 1) {
        exit('
            CLE error message: Database connection failed, script execution terminated.
            <br /><br />
            Please check your database connection settings in conf/config.xml<br />
                (if you do not wish to automatically start DB connection, you can as well deactivate it).
            <br /><br />
            If you do not wish CLE to terminate script execution on DB connection error,<br />
                you can set that in configuration file as well.
            <br /><br />
        ');
    }
}

/* Autoload of classes */
function __autoload($class)
{
    global $_autoloadArray;

    if (in_array($class, $_autoloadArray)) {
        require_once $_autoloadArray[$class];
    }
}

/* Let's find out, what we will include and get some $_id stuff, if available */
$_fileToInclude = CL_SEO::getInstance()->getFileToInclude().'.php';

/* Let's fire up modules */
$_CLE_Modules = CLE_Modules::getInstance();

/* Let's load some page! */
require_once $_fileToInclude;

/*
 * If "normal page" is requested
 * (not just some file-sending page)
 * It sets some variables and echoes the whole HTML code
 * (viz that file)
 */
if (@!defined(CLE_DONT_LOAD_LAYOUT)) {
    require_once './sys/page.php';
}
?>
