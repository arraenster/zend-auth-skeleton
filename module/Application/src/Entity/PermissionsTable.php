<?php
namespace Application\Entity;

use Application\Entity\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User permissions
 *
 * @ORM\Table(name="permissions_table")
 * @ORM\Entity
 *
 * @author: Vladyslav Semerenko <vladyslav.semerenko@gmail.com>
 */
class PermissionsTable extends BaseEntity
{

    /**
     * @var int
     * @ORM\Column(type="integer", length=3, nullable=false)
     */
    protected $userId;

    /**
     * @var int
     * @ORM\Column(type="integer", length=3, nullable=false)
     */
    protected $doorId;

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return int
     */
    public function getDoorId()
    {
        return $this->doorId;
    }

    /**
     * @param $doorId
     * @return $this
     */
    public function setDoorId($doorId)
    {
        $this->doorId = $doorId;
        return $this;
    }
}
