<?php
namespace Auth\Model;

use Zend\Crypt\Password\Bcrypt;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

/**
 *
 * @author: Vladyslav Semerenko <vladyslav.semerenko@gmail.com>
 */
class AuthAdapter implements AdapterInterface
{

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceLocator;

    public function setServiceLocator($sl)
    {

        return $this->serviceLocator = $sl;
    }

    public function setCredentials($username, $password)
    {

        $this->username = $username;
        $this->password = $password;

        return $this;
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *               If authentication cannot be performed
     */
    public function authenticate()
    {

        $objectManager = $this->serviceLocator->get('Doctrine\ORM\EntityManager');

        $user = $objectManager
            ->getRepository('\Application\Entity\UsersTable')
            ->findOneBy(['username' => $this->username]);

        if ($user) {
            $bcrypt = new Bcrypt();
            if ($bcrypt->verify($this->password, $user->getPassword())) {
                return new Result(
                    Result::SUCCESS,
                    $user
                );
            } else {
                return new Result(
                    Result::FAILURE_CREDENTIAL_INVALID,
                    $user,
                    ['Bad password']
                );
            }
        } else {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                $user,
                ['Username did not found']
            );
        }
    }
}