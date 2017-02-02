<?php
namespace Application\Entity;

use Application\Entity\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Doors
 *
 * @ORM\Table(name="doors_table")
 * @ORM\Entity
 *
 * @author: Vladyslav Semerenko <vladyslav.semerenko@gmail.com>
 */
class DoorsTable extends BaseEntity
{

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    protected $alias;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $description;

    /**
     * @var int
     * @ORM\Column(type="integer", length=1, nullable=false)
     */
    protected $status;

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param $alias
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
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

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
}
