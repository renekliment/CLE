<?php
/**
 * File containing class for inheriting JSON access classes
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
 * Class for inheriting JSON access classes
 *
 * @package CLE
 * @subpackage Classes
 */
abstract class CLE_JSON
{
    /**
     * @var bool was the token already verified during this request?
     */
    protected $tokenVerified = FALSE;

    /**
     * @var bool if the token was already verified, what was the status?
     */
    protected $tokenVerifiedStatus = FALSE;

    /**
     * Generates a JSON token
     *
     * @return void
     */
    protected function tokenGenerate()
    {
        $_SESSION['json_token'] = md5(microtime().rand(0,1000000));
    }

    /**
     * Returns the JSON token
     *
     * @return array array containing the JSON token
     */
    public function tokenGet()
    {
        if (!isset($_SESSION['json_token'])) {
            $this->tokenGenerate();
        }

        return array('json_token' => $_SESSION['json_token']);
    }

    /**
     * Verifies a given JSON token
     *
     * @param $token token sent by user
     * @return bool do the tokens match?
     */
    protected function tokenVerify($token)
    {
        if ($this->tokenVerified) {
            return $this->tokenVerifiedStatus;
        } else {
            $this->tokenVerified = TRUE;

            if ($_SESSION['json_token'] AND ($token == $_SESSION['json_token'])) {
                $this->tokenVerifiedStatus = TRUE;
                $this->tokenGenerate();
            }

            return $this->tokenVerifiedStatus;
        }
    }

}

?>