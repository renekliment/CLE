<?php
/**
 * @package CLE
 * @subpackage API
 * @author Rene Kliment <rene.kliment@gmail.com>
 * @version 1.1
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License - Version 3, 19 November 2007
 *
 * This software is dual-licensed - it is also available under a commercial license,
 * so there's possibility to use this software or its parts in software which is not license-compatible.
 * For more information about licensing, contact the author.
 */

define('CLE_DONT_LOAD_LAYOUT', 'TRUE');
unset($_CL_Templates);

if (isset($_stringId)
    AND in_array($_stringId, $_jsonEnabledModules)
    AND isset($_GET['a'])
) {
    $handlingClassName = $_stringId.'_JSON';
    $handlingClass = new $handlingClassName;

    if (isset($_GET['a']) AND method_exists($handlingClass, $_GET['a'])) {
        echo json_encode(
            $handlingClass->$_GET['a']($_GET)
        );
    }
}

?>