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

namespace FormBundle\Entity;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\Users\Person,
    CommonBundle\Component\Util\Url,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\FormField")
 * @ORM\Table(name="forms.field")
 */
class FormField
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
     * @var FormBundle\Entity\Nodes\FormSpecification The form this field is part of.
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Nodes\FormSpecification", inversedBy="fields")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id")
     */
    private $form;

    /**
     * @var string The type of this field.
     *
     * @ORM\Column(name="type", type="string")
     */
    private $type;

    /**
     * @var int The order of this field.
     *
     * @ORM\Column(name="fieldOrder", type="bigint")
     */
    private $order;

    /**
     * @var string The label of this field.
     *
     * @ORM\Column(name="label", type="string")
     */
    private $label;

    /**
     * @var boolean Indicates whether this is a required field.
     *
     * @ORM\Column(name="required", type="boolean")
     */
    private $required;

    /**
     * @param string $label
     */
    public function __construct($form, $type, $order, $label, $required)
    {
        $this->type = $type;
        $this->form = $form;
        $this->order = $order;
        $this->label = $label;
        $this->required = $required;
    }

    /**
     * @return The identification number of this form.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return The form this field belongs to.
     */
    public function getForm() {
        return $this->form;
    }

    /**
     * @param string $label
     *
     * @return \FormBundle\Entity\FormField
     */
    public function setLabel($label) {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * @param int $order
     *
     * @return \FormBundle\Entity\FormField
     */
    public function setOrder($order) {
        $this->order = $order;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * @param string $type
     *
     * @return \FormBundle\Entity\FormField
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param boolean $required
     *
     * @return \FormBundle\Entity\FormField
     */
    public function setRequired($required) {
        $this->required = $required;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRequired() {
        return $this->required;
    }

}
