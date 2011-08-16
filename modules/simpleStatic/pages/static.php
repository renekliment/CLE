<?php
/**
 * @package CLE-Modules
 * @subpackage simpleStatic
 * @author Rene Kliment <rene.kliment@gmail.com>
 * @version 1.1
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License - Version 3, 19 November 2007
 *
 * This software is dual-licensed - it is also available under a commercial license,
 * so there's possibility to use this software or its parts in software which is not license-compatible.
 * For more information about licensing, contact the author.
 */

if (isset($_stringId) AND $_stringId) {
    $dir = 'data/simpleStatic/';

    $urlArray = CL_SEO::getInstance()->URLArray;

    $h = dir($dir);
    while (false !== ($file = $h->read())) {
        if ($_stringId.'.html' == $file) {
            $navString = CL::getConf('simpleStatic/'.$_stringId)
                         ? CL::getConf('simpleStatic/'.$_stringId)
                         : $_stringId;

            $_CL->setNavigation($navString);
            $_CL_Templates->set(array(
                ''                   => file_get_contents($dir.$_stringId.'.html'),
                'simpleStatic-title' => $navString,
            ));
        }
    }
}
?>