<?php
/**
 * @package CLE
 * @subpackage SysFiles
 * @author Rene Kliment <rene.kliment@gmail.com>
 * @version 1.1
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License - Version 3, 19 November 2007
 *
 * This software is dual-licensed - it is also available under a commercial license,
 * so there's possibility to use this software or its parts in software which is not license-compatible.
 * For more information about licensing, contact the author.
 */

$_CL_Xety = $GLOBALS['_CL_Xety'] = CL_Xety::getInstance();
$_CL = $GLOBALS['_CL'];

/* And set default language (if not set other) */
if (!isset($_SESSION['language']) OR empty($_SESSION['language'])) {
    $_SESSION['language'] = (string)CL::getConf('main/default_language');
}

if (isset($_SESSION['_sessionSetMessageBuffer']) AND $_SESSION['_sessionSetMessageBuffer']) {
    $_CL->setMessage($_SESSION['_sessionSetMessageBuffer']);
    unset($_SESSION['_sessionSetMessageBuffer']);
}

if (isset($GLOBALS['_setMessageBuffer']) AND $GLOBALS['_setMessageBuffer']) {
    $_CL->setMessage(constant($GLOBALS['_setMessageBuffer']));
}

/* Let's load default layout */
CL_Templates::setDirectory(CL::getConf('CL_Templates/dir_default'));
if (@!defined(CLE_DONT_LOAD_LAYOUT)) {
    $_CL_Templates = $GLOBALS['_CL_Templates'] = CL_Templates::getInstance();
}
?>
