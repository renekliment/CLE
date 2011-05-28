<?php
/**
 * File containing base class of framework
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
 * Base class of framework
 *
 * Includes for example: security tokens methods, date / timestamp convertion methods,
 * methods for user navigation, some paging functions, etc.
 * @package CL
 */
class CL
{
    /**
     * @var string configuration file
     */
    static $configFile = './conf/config.xml';

    /**
     * @var object configuration file handler
     */
    static $configHandler;

    /**
     * @var string the message for user to notificate
     */
    protected $message = '';
    
    /**
     * @var string the navigation string
     */
    protected $navigation = '';

    /**
     * @var bool was the security token already verified?
     */
    protected static $tokenVerified = FALSE;

    /**
     * @var object the instance of the class (Singleton pattern)
     */
    private static $instance;

    /**
     * Things like session_start, generating of tokens against CSRF, etc
     */
    function __construct()
    {
        /* Wanna redirect somewhere else? */
        if (isset($_GET['r']) AND self::getConf('CL/allowRedirect') == 'TRUE') {
            header('Refresh: 0; url='.$_GET['r']);
            exit;
        }
        
        /* Sessions start, let's roll guys, with sessions, the real fun begins! Yeah! (Take care of them, avoid SID stealing) */
        if (!isset($_SESSION)) {
            session_start();
        }

        /* Let's generate security tokens */
        if (!isset($_SESSION['token']) OR !isset($_SESSION['token_name'])) {
            self::tokenGenerate();
        }
    }

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
     * Inits configuration file handler
     */
    static function initConfHandler()
    {
        if (!count(self::$configHandler)) {
            self::$configHandler = simplexml_load_file(self::$configFile);
        }   
    }

    /**
     * Returns value of config variable
     *
     * @param string $name what we want
     * @return string wanted value
     */
    static function getConf($name)
    {
        self::initConfHandler();
        
        $array = explode('/', $name);
        $f = $array[0];
        $s = $array[1];

        return (string)self::$configHandler->$f->$s;
    }

    /**
     * Generates token against CSRF
     */
    static function tokenGenerate()
    {
        $_SESSION['token'] = md5(microtime().rand(0,1000000));
        $_SESSION['token_name'] = substr(md5(microtime().rand(0,1000000)),0,8);
    }

    /**
     * Verifies token sent by user
     *
     * @return boolean do the tokens match?
     */
    function tokenVerify()
    {
        if (self::$tokenVerified) {
            return TRUE;
        } else {
            $name = $_SESSION['token_name'];
            if (isset($_POST[$name]) AND $_POST[$name] == $_SESSION['token']) {
                self::$tokenVerified = TRUE;
            }
            self::tokenGenerate();

            return self::$tokenVerified;
        }
    }

    /**
     * Generates random salt
     *
     * @return string random salt
     */
    function getRandomSalt()
    {
        return substr(md5(microtime().rand(0, 1000000)), 0, 5);
    }

    /**
     * This function verifies, if all of the $_POST params are set
     *
     * @param array $array array of parameters
     * @return bool this is TRUE, if all of the wanted $_POST[] are set, else FALSE
     */
    function postIsset($array)
    {
        foreach ($array as $item) {
            if (!isset($_POST[$item])) {
                return FALSE;
            }
        }
        
        return TRUE;
    }

    /**
     * This function is almost like $this->postIsset(),
     * except the fact, that it also verifies, if the value of variable is TRUE
     * (for example the form fields must be filled in, if == '', it won't pass)
     *
     * @param array $array array of parameters
     * @return bool returns (bool)everything as expected
     */
    function postIssetAndTrue($array)
    {
        foreach ($array as $item) {
            if (!isset($_POST[$item]) OR !trim($_POST[$item])) {
                return FALSE;
            }
        }
        
        return TRUE;
    }

    /**
     * Pretty useful function for dealing with a lot of POST parameters
     *
     * ->getPostParamsAsArray(array('name', 'description'))
     * returns array('name' => $_POST['name'], 'description' => $_POST['description'])
     *
     * @param array $params array of names of POST parameters
     * @return array array of POST parameters
     */
    function getPostParamsAsArray($params)
    {
        $array = array();
        foreach ($params as $param) {
            $array[$param] = & $_POST[$param];
        }

        return $array;
    }

    /**
     * Sets the message for user to notificate
     *
     * @param string $message Represents the message for user to notificate
     */
    function setMessage($message)
    {
        $this->message .= $message."\n";
    }

    /**
     * Returns the message for user to notificate
     *
     * @return string returns the message for user to notificate
     */
    function getMessage()
    {
        return $this->message;
    }

    /**
     * Sets the navigation string
     *
     * @param string $navigation Represents the navigation string
     */
    function setNavigation($navigation)
    {
        $this->navigation = $navigation;
    }

    /**
     * Gets the navigation string
     *
     * @return string returns the navigation string
     */
    function getNavigation()
    {
        return $this->navigation;
    }

    /**
     * Gets the user's IP (including some other IPs like proxy IP, etc)
     *
     * @return string the user's IP (including some other IPs like proxy IP, etc)
     */
    function getIPString()
    {
        $ip = $_SERVER["REMOTE_ADDR"];

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip .= '|'.$_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ip .= '|'.$_SERVER['HTTP_FORWARDED'];
        }

        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip .= '|'.$_SERVER['HTTP_CLIENT_IP'];
        }

        if (isset($_SERVER['X_HTTP_FORWARDED_FOR'])) {
            $ip .= '|'.$_SERVER['X_HTTP_FORWARDED_FOR'];
        }

        if (isset($_SERVER['X_FORWARDED_FOR'])) {
            $ip .= '|'.$_SERVER['X_FORWARDED_FOR'];
        }

        $ip .= "/".gethostbyaddr($_SERVER["REMOTE_ADDR"]);

        return $ip;
    }

    /**
     * Converts the unix timestamp to the date in this format: "Y-m-d H:i:s"
     *
     * @param integer $timestamp the timestamp to convert
     * @return string the date in this format: "Y-m-d H:i:s"
     */
    function getDate($timestamp)
    {
        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * The real oppisite of getDate(); This function converts the date in this format: "Y-m-d H:i:s" to the unix timestamp
     *
     * @param string $date the date (Y-m-d H:i:s) to convert to timestamp
     * @return integer the timestamp
     */
    function getTimestamp($date)
    {
        $year = substr($date,0,4);
        $month = substr($date,5,2);
        $day = substr($date,8,2);

        $h = substr($date,11,2);
        $m = substr($date,14,2);
        $s = substr($date,17,2);

        return mktime($h,$m,$s,$month,$day,$year);
    }

    /**
     * This function counts and return information needed for listing some data, which are divided into pages or so.
     * Useful for example when listing guestbook items from database, according to the given page number (ex. gb.php?page=5),
     * this function verifies, if the given page number is correct, counts number of pages and also counts beginning - the id of item where start listing from.
     *
     * @param integer $items count of all items
     * @param integer $items_per_page count of items per page
     * @param integer $page given number of page
     * @return array all information needed to list exactly the data we want
     */
    function paging($items, $items_per_page, $page=0)
    {
        $returnArray = array();
        $returnArray['items'] = $items;
        $returnArray['items_per_page'] = $items_per_page;
        $returnArray['pages'] = ceil($items/$items_per_page);
        $returnArray['page'] = (int)$page;
        if(!$returnArray['page'] AND isset($_GET['p'])) {
            $returnArray['page'] = (int)$_GET['p'];
        }

        if (!$returnArray['page'] OR empty($returnArray['page']) OR $returnArray['page'] > $returnArray['pages']) {
            $returnArray['page'] = 1;
        }

        $returnArray['begin'] = ($returnArray['page']-1) * $items_per_page;
        $returnArray['items_on_page'] = ($returnArray['items'] - $returnArray['begin']) > $returnArray['items_per_page']
                                        ? $returnArray['items_per_page']
                                        : $returnArray['items'] - $returnArray['begin'];

        return $returnArray;
    }

    /**
     * Send file to default output under given name
     *
     * @param string $file file to download (full path is nice)
     * @param string $name name of the file for download (you can choose another name, than original)
     */
    function downloadFile($file, $name)
    {
        $size = filesize($file);

        header("Content-Type: application/x-octet-stream");
        header("Content-Disposition: Attachment; filename=\"".$name."\"");
        header('Expires: '.gmdate("D, d M Y H:i:s", time()+7200).' GMT');
        header('Accept-Ranges: bytes');
        header('Cache-control: no-cache, must-revalidate');
        header('Pragma: private');
        header("Content-Length: ".$size);

        @ob_flush();
        @flush();
        @ob_end_flush();

        $chunksize = 1*(1024*1024); // how many bytes per chunk
        $handle = fopen($file, 'rb');

        if ($handle === false) {
            return false;
        }

        while (!feof($handle)) {
            echo fread($handle, $chunksize);
        }

        fclose($handle);
    }

    /**
     * If there is no cal_days_in_month() function, we can simply handle it :-)
     *
     * @param string $cal calendar type
     * @param integer $month month
     * @param integer $year year
     * @return integer days in month
     */
    function calDaysInMonth($cal=CAL_GREGORIAN, $month, $year)
    {
        if (function_exists('cal_days_in_month')) {
            return cal_days_in_month($cal,$month,$year);
        } else {
            return date('t', mktime(0, 0, 0, $month+1, 0, $year));
        }
    }

    /**
     * Returns extension of a file according to the given MIME type
     *
     * @param string $mime MIME type of the file
     * @return string file extension
     */
    function mime2extension($mime)
    {
        $handler = self::$configHandler;
        $r = $handler->xpath("/CL_config/goodFileTypes/item[@mime='".$mime."']");
        return $r[0]['extension'];
    }

    /**
     * Paging thing - user output :-)
     *
     * @global array $paging the array generated by CL::paging() function, includes some calculations
     * @param string $what N of what? it will return like "30 pictures altogether"
     * @param string $param when we need add some other things into the URL
     * @return string if it is needed, it will return the listing
     */
    function pages($what, $param='')
    {
        global $paging;

        if ($paging['items'] > $paging['items_per_page']) {
            $layout = new CL_Templates('##base.tpl', 'PAGING-MAIN');

            if ($paging['page'] != 1) {
                $layout->add('data', $layout->getTpl('##base.tpl', 'PAGING-LEFT'));
                $layout->set('page-previous', $paging['page'] -1);
            }

            $itemsLeft =  $paging['page'] - (1 + 1);

            /*$itemsRight = $paging['page'] == $paging['pages']
                          ? 0
                          : $paging['pages'] - ($paging['page'] + 1);
            */
            $itemsRight = $paging['pages'] - ($paging['page'] + 1);

            $itemsLeftShow = ($itemsLeft < 4) ? $itemsLeft : 4;
            $itemsRightShow = ($itemsRight < 4) ? $itemsRight : 4;

            if ($itemsLeftShow < 4 AND $itemsRight > 4) {
                $itemsRightShow = 8 - $itemsLeftShow;
                $itemsRightShow = $itemsRight >= $itemsRightShow  ? $itemsRightShow : $itemsRight;
            } elseif ($itemsRightShow < 4 AND $itemsLeft > 4) {
                $itemsLeftShow = 8 - $itemsRightShow;
                $itemsLeftShow = $itemsLeft >= $itemsLeftShow  ? $itemsLeftShow : $itemsLeft;
            }

            /* First page */
            $layout->add('data', $layout->getTpl('##base.tpl', 'PAGING-VALUE'));
            $layout->set('page', '1');

            /* Spacer of the first page */
            if ($paging['page'] - $itemsLeftShow != 1 + 1) {
                $layout->add('data', ' ... ');
            }

            /* Left side */
            for ($i = $itemsLeftShow; $i >= 0; $i--) {
                if ($paging['page'] - $i != $paging['pages'])
                $layout->add('data', $layout->getTpl('##base.tpl', 'PAGING-VALUE'));
                $layout->set('page', $paging['page'] - $i);
            }

            /* Right side */
            for ($i = 1; $i < $itemsRightShow + 1; $i++) {
                $layout->add('data', $layout->getTpl('##base.tpl', 'PAGING-VALUE'));
                $layout->set('page', $paging['page'] + $i);
            }

            /* Spacer of the last page */
            if ($paging['page'] + $itemsRightShow != $paging['pages'] - 1) {
                $layout->add('data', ' ... ');
            }

            /* Last page */
            $layout->add('data', $layout->getTpl('##base.tpl', 'PAGING-VALUE'));
            $layout->set('page', $paging['pages']);

            if ($paging['page'] != $paging['pages']) {
                $layout->add('data', $layout->getTpl('##base.tpl', 'PAGING-RIGHT'));
                $layout->set(array(
                    'page-next' => $paging['page'] + 1,
                    'page-last' => $paging['pages'],
                ));
            }

            /* Set some info stuff */
            $layout->set(array(
                'items' => $paging['items'],
                'what'  => $what,
                'param' => $param,
            ));

            /* Tag the currtent page and return output */
            $search = str_replace(
                        '${param}',
                        $param,
                        str_replace('${page}', $paging['page'],$layout->getTpl('##base.tpl', 'PAGING-VALUE'))
            );

            $replace = str_replace(
                        '${param}',
                        $param,
                        str_replace('${page}', $paging['page'],$layout->getTpl('##base.tpl', 'PAGING-BVALUE'))
            );

            return str_replace($search, $replace, $layout->getContent());
        }

        return '';
    }

    /**
     * Return HTML <a> code for a link
     *
     * @param string $link link
     * @param string $name name of the link (showed text)
     * @return string HTML code
     */
    function getLinkHTML($link, $name='')
    {
        $_CL_Xety = CL_Xety::getInstance();

        if (!$name) {
            $name = 'http://'.$link;
        }

        $layout = new CL_Templates('##base.tpl', 'LINK');
        $layout->set(array(
            'link-www'  => $_CL_Xety->plain($link),
            'link-name' => $_CL_Xety->plain($name),
        ));

        return $layout->getContent();
    }

    /**
     * Return HTML <a> code for an e-mail address
     *
     * @param string $email e-mail address
     * @param string $name name of the link (showed text)
     * @return string HTML code
     */
    function getEmailHTML($email, $name='')
    {
        $_CL_Xety = CL_Xety::getInstance();

        if (!$name) {
            $name = $email;
        }

        $layout = new CL_Templates('##base.tpl', 'EMAIL');
        $layout->set(array(
            'email'      => $_CL_Xety->plain($email),
            'email-name' => $_CL_Xety->plain($name),
        ));

        return $layout->getContent();
    }
}
?>