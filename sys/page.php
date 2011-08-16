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

/* Let's include CSS files for modules */
$themeCSSs = array();
$themeCSSdir = CL::getConf('CL_Templates/dir').CL::getConf('CL_Templates/dir_default').'css_modules/';
if (file_exists($themeCSSdir)) {
    $themeCSSs = scandir($themeCSSdir);

    foreach ($themeCSSs as $css) {
        if ($css != '.' AND $css != '..')
        $_addVarsArray[]['header-add'] = '    <link href="${nonRootPrefix}'.$themeCSSdir.$css.'" rel="stylesheet" type="text/css" media="screen" />'."\n";
    }

}

$defaultCSSdir = CL::getConf('CL_Templates/dir').'default/css_modules/';
if (file_exists($defaultCSSdir)) {
    $defaultCSSs = scandir($defaultCSSdir);

    foreach ($defaultCSSs as $css) {
        if (!in_array($css, $themeCSSs) AND $css != '.' AND $css != '..') {
            $_addVarsArray[]['header-add'] = '    <link href="${nonRootPrefix}'.$defaultCSSdir.$css.'" rel="stylesheet" type="text/css" media="screen" />'."\n";
        }
    }

}

/* Are there any other variables to add? (for example from init stage of modules) */
if (count($_addVarsArray)) {
    foreach ($_addVarsArray as $v1) {
	    $_CL_Templates->add($v1);
    }
}

/* Are there any other variables to set? (for example from init stage of modules) */
if (count($_setVarsArray)) {
    $_CL_Templates->set($_setVarsArray);
}

/* Set some info to head tag, verification tokens, navigation, ... */
$_CL_Templates->set(array(
    'footer'                    => CL::getConf('main/footer'),
    'header-language'           => CL::getConf('main/default_language'),
    'header-title'              => CL::getConf('main/website_name'),
    'header-description'        => CL::getConf('main/website_description'),
    'header-keywords'           => CL::getConf('main/website_keywords'),
    'header-author'             => CL::getConf('main/website_webmaster'),
    'header-add'                => '',
    '_id'                       => $_id,
    'address_ssl'               => CL::getConf('main/address_ssl'),
    'nonRootPrefix'             => CL::getConf('main/nonRootPrefix'),
    'token'                     => $_SESSION['token'],
    'token-name'                => $_SESSION['token_name'],
    'currentYear'               => date('Y'),
    'header-navigation'         => strip_tags($_CL->getNavigation()),
    'message'                   => $_CL->getMessage()
                                   ? str_replace(
                                           '${message}', 
                                           $_CL_Xety->basic($_CL->getMessage()),
                                           $_CL_Templates->getTpl('##base.tpl', 'MESSAGE')
                                     )
                                   : '',
    'layout-directory'          => $_CL_Templates->getDirectory(),
    'layout-directory-fullpath' => CL::getConf('CL_Templates/dir').
                                   $_CL_Templates->getDirectory(),
    'layout-css'                => CL::getConf('main/nonRootPrefix').
                                   CL::getConf('CL_Templates/dir').
                                   $_CL_Templates->getDirectory().
                                   '/default.css',
));

/* Any cookie variables to fill into layout vars? */
if (isset($_autoFillInCookieVars)) {
    $tempArray = array();
    foreach ($_autoFillInCookieVars as $var) {
        if (isset($_POST[$var]) AND $_POST[$var]) {
            $_COOKIE[$var] = $_POST[$var];
            setcookie($var,
                      $_POST[$var],
                      time()+3600*24*365,
                      CL::getConf('main/nonRootPrefix')
            );

        }
        
        $tempArray['_autoFillInCookieVars-'.$var] = isset($_COOKIE[$var])
                                                    ? $_CL_Xety->plain($_COOKIE[$var])
                                                    : '';
    }
    $_CL_Templates->set($tempArray);
}

/* At last, any $_POST variables? */
if (isset($_autoFillInPostVars)) {
    $tempArray = array();
    foreach ($_autoFillInPostVars as $var) {
        $tempArray['_autoFillInPostVars-'.$var] = (isset($_POST[$var]) AND $_POST[$var])
                                                  ? $_CL_Xety->plain($_POST[$var])
                                                  : '';
    }
    $_CL_Templates->set($tempArray);
}
/* Finally, send output to user ... */
echo $_CL_Templates->getContent();
?>