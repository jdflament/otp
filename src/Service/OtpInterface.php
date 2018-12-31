<?php

namespace App\Service;

use App\Entity\User;

/**
 * @author Jean-David Flament <flamentjeandavid@yahoo.fr>
 * @author Thomas Debacker <dbkr.thomas@gmail.com>
 */
interface OtpInterface
{
    /**
     * Generates an authorization code for a given password
     *
     * @param string $password
     * @param int    $length
     *
     * @return string
     */
    public function generateCode($password, $length = 6);

    /**
     * Send the authorization code by email to the User
     *
     * @param int  $code
     * @param User $user
     *
     * @return int
     */
    public function sendCode($code, User $user);

    /**
     * Store the authorization code for a given User
     *
     * @param      $code
     * @param User $user
     */
    public function storeCode($code, User $user);

    /**
     * Check if the authorization code is valid
     *
     * @param      $code
     * @param User $user
     *
     * @return int
     */
    public function checkCode($code, User $user);
}
