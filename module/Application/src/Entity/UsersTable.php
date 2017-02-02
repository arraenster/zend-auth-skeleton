<?php
namespace Application\Entity;

use Application\Entity\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Main users table
 *
 * @ORM\Table(name="users_table")
 * @ORM\Entity
 * @author: Vladyslav Semerenko <vladyslav.semerenko@gmail.com>
 */
class UsersTable extends BaseEntity
{

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $password;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $role;

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;
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
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param $role
     * @return $this
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }
}
