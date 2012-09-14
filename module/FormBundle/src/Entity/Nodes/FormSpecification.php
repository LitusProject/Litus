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

namespace FormBundle\Entity\Nodes;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\Users\Person,
    CommonBundle\Component\Util\Url,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Nodes\FormSpecification")
 * @ORM\Table(name="nodes.form")
 */
class FormSpecification extends \CommonBundle\Entity\Nodes\Node
{

    /**
     * @var string The title of this form.
     *
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var string The introduction of this form
     *
     * @ORM\Column(type="text")
     */
    private $introduction;

    /**
     * @var string The text of the submit button of this form.
     *
     * @ORM\Column(type="text")
     */
    private $submitText;

    /**
     * @var int The maximum number of entries of this form.
     *
     * @ORM\Column(name="max", type="integer")
     */
    private $max;

    /**
     * @var boolean Indicates whether submitters can submit more than different one answer.
     *
     * @ORM\Column(name="multiple", type="boolean")
     */
    private $multiple;

    /**
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\FormField", mappedBy="form")
     */
    private $fields;

    /**
     * @var DateTime The start date and time of this form.
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var DateTime The end date and time of this form.
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var boolean The flag whether the banner is active or not.
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param string $title
     * @param boolean $redoable
     * @param boolean $multiple
     */
    public function __construct($person, $title, $introduction, $submitText, $startDate, $endDate, $active, $max = 0, $multiple = false)
    {
        parent::__construct($person);

        $this->title = $title;
        $this->introduction = $introduction;
        $this->submitText = $submitText;
        $this->max = $max;
        $this->multiple = $multiple;
        $this->fields = new ArrayCollection();
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->active = $active;
    }

    /**
     * @param string $title
     *
     * @return \FormBundle\Entity\Nodes\FormSpecification
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $introduction
     *
     * @return \FormBundle\Entity\Nodes\FormSpecification
     */
    public function setIntroduction($introduction) {
        $this->introduction = $introduction;
        return $this;
    }

    /**
     * @return string
     */
    public function getIntroduction() {
        return $this->introduction;
    }

    /**
     * @param string $submitText
     *
     * @return \FormBundle\Entity\Nodes\FormSpecification
     */
    public function setSubmitText($submitText) {
        $this->submitText = $submitText;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubmitText() {
        return $this->submitText;
    }

    /**
     * @param int $max
     *
     * @return \FormBundle\Entity\Nodes\FormSpecification
     */
    public function setMax($max) {
        $this->max = $max;
        return $this;
    }

    /**
     * @return int
     */
    public function getMax() {
        return $this->max;
    }

    /**
     * @param boolean $multiple
     *
     * @return \FormBundle\Entity\Nodes\FormSpecification
     */
    public function setMultiple($multiple) {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isMultiple() {
        return $this->multiple;
    }

    /**
     * @param FormBundle\Entity\FormField The field to add to this form.
     */
    public function addField($field) {
        $this->fields->add($field);
        return $this;
    }

    public function getFields() {
        return $this->fields->toArray();
    }

    /**
     * @param DateTime $startDate
     *
     * @return \BannerBundle\Entity\Nodes\FormSpecification
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
     * @return \BannerBundle\Entity\Nodes\FormSpecification
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
     * @return \BannerBundle\Entity\Nodes\FormSpecification
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
}
