<?php
namespace Application\Entity;

use Application\Entity\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Doors
 *
 * @ORM\Table(name="log_table")
 * @ORM\Entity
 *
 * @author: Vladyslav Semerenko <vladyslav.semerenko@gmail.com>
 */
class LogTable extends BaseEntity
{

    /**
     * @var int
     * @ORM\Column(type="integer", length=3, nullable=false, unique=true)
     */
    protected $doorId;

    /**
     * @var int
     * @ORM\Column(type="integer", length=3, nullable=false)
     */
    protected $userId;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", length=255, nullable=false, unique=true)
     */
    protected $logTime;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    protected $description;

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
     * @param $logTime
     * @return \DateTime
     */
    public function getLogTime($logTime)
    {
        return $this->logTime;
    }

    /**
     * @param $logTime
     * @return $this
     */
    public function setLogTime($logTime)
    {
        $this->logTime = $logTime;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
}