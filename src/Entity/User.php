<?php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Jean-David Flament <flamentjeandavid@yahoo.fr>
 * @author Thomas Debacker <dbkr.thomas@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="auth_code", type="string", nullable=true)
     */
    private $otpCode;

    /**
     * @return mixed
     */
    public function getOtpCode()
    {
        return $this->otpCode;
    }

    /**
     * @param mixed $otpCode
     */
    public function setOtpCode($otpCode)
    {
        $this->otpCode = $otpCode;
    }

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}
