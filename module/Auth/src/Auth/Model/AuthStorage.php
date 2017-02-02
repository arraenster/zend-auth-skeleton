<?php

namespace Auth\Model;

use Zend\Authentication\Storage;

/**
 * Custom storage class
 * Set remember me value
 *
 */
class AuthStorage extends Storage\Session
{

    /**
     * How long user can be logged in
     *
     * @var int
     */
    const REMEMBER_TIME = 86400;

    /**
     * Set custom remember time
     *
     * @param int $rememberMe
     * @param int $time
     */
    public function setRememberMe($rememberMe = 0, $time = self::REMEMBER_TIME)
    {

        if ($rememberMe == 1) {
            $this->session->getManager()->rememberMe($time);
        }
    }

    /**
     * Custom forget me method
     */
    public function forgetMe()
    {

        $this->session->getManager()->forgetMe();
    }
}
