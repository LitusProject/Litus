<?php

namespace FormBundle\Entity\Node\Form;

use Doctrine\ORM\Mapping as ORM;
use FormBundle\Entity\Node\Form;
use FormBundle\Entity\Node\Group;

/**
 * This entity stores mapping of forms and groups
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Node\Form\GroupMap")
 * @ORM\Table(name="nodes_forms_groups_map")
 */
class GroupMap
{
    /**
     * @var integer The ID of this mapping
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Form The form of this mapping
     *
     * @ORM\OneToOne(targetEntity="FormBundle\Entity\Node\Form")
     * @ORM\JoinColumn(name="form", referencedColumnName="id")
     */
    private $form;

    /**
     * @var Group The group of this mapping
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Node\Group", inversedBy="forms")
     * @ORM\JoinColumn(name="form_group", referencedColumnName="id")
     */
    private $group;

    /**
     * @var integer The order of this form
     *
     * @ORM\Column(name="group_order", type="smallint")
     */
    private $order;

    /**
     * @param Form    $form  The form of this mapping
     * @param Group   $group The group of this mapping
     * @param integer $order
     */
    public function __construct(Form $form, Group $group, $order)
    {
        $this->form = $form;
        $this->group = $group;
        $this->order = $order;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param  Form $form
     * @return self
     */
    public function setForm(Form $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param  Group $group
     * @return self
     */
    public function setGroup(Group $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param  integer $order
     * @return self
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }
}
