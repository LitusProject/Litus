<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BannerBundle\Entity\Nodes;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\Users\Person,
    CommonBundle\Component\Util\Url,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="BannerBundle\Repository\Nodes\Banner")
 * @ORM\Table(name="nodes.banner")
 */
class Banner extends \CommonBundle\Entity\Nodes\Node
{

    /**
     * @var The reservation's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The name of the banner
     *
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @var DateTime The start date and time of this reservation.
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime The end date and time of this reservation.
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var string The location of the image
     *
     * @ORM\Column(type="text")
     */
    private $image;

    /**
     * @var boolean The flag whether the banner is active or not.
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var string The link for this banner
     *
     * @ORM\Column(type="text")
     */
    private $url;

    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param string $category
     */
    public function __construct(Person $person, $name, $image, DateTime $startDate, DateTime $endDate, $active, $url )
    {
        parent::__construct($person);

        $this->name = $name;
        $this->image = $image;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->active = $active;
        $this->url = $url;
    }

    /**
     * @param string $name
     *
     * @return \BannerBundle\Entity\Nodes\Notification
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $image
     *
     * @return \BannerBundle\Entity\Nodes\Notification
     */
    public function setImage($image) {
        $this->image = $image;
        return $this;
    }

    /**
     * @return string
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * @param DateTime $startDate
     *
     * @return \BannerBundle\Entity\Nodes\Notification
     */
    public function setStartDate($startDate) {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartDate() {
        return $this->startDate;
    }

    /**
     * @param DateTime $endDate
     *
     * @return \BannerBundle\Entity\Nodes\Notification
     */
    public function setEndDate($endDate) {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndDate() {
        return $this->endDate;
    }

    /**
     * @param boolean $active
     *
     * @return \BannerBundle\Entity\Nodes\Notification
     */
    public function setActive($active) {
        $this->active = $active;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive() {
        return $this->active;
    }

    /**
     * @param string $url
     *
     * @return \BannerBundle\Entity\Nodes\Notification
     */
    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }
}
