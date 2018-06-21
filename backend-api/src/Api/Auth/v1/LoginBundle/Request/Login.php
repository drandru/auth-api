<?php

namespace Api\Auth\v1\LoginBundle\Request;

use DRD\GeneralBundle\Request\RequestInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Login implements RequestInterface
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $login;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $password;

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return Login
     */
    public function setLogin(string $login): Login
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return Login
     */
    public function setPassword(string $password): Login
    {
        $this->password = $password;

        return $this;
    }
}
