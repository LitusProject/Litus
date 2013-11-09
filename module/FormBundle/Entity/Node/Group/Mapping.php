<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace FormBundle\Entity\Node\Group;

use FormBundle\Entity\Node\Form,
    FormBundle\Entity\Node\Group,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores mapping of forms and groups
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Node\Group\Mapping")
 * @ORM\Table(name="nodes.form_groups_mapping")
 */
class Mapping
{
    /**
     * @var int The ID of this mapping
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \FormBundle\Entity\Node\Form The form of this mapping
     *
     * @ORM\OneToOne(targetEntity="FormBundle\Entity\Node\Form")
     * @ORM\JoinColumn(name="form", referencedColumnName="id")
     */
    private $form;

    /**
     * @var \FormBundle\Entity\Node\Group The group of this mapping
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Node\Group", inversedBy="forms")
     * @ORM\JoinColumn(name="form_group", referencedColumnName="id")
     */
    private $group;

    /**
     * @var int The order of this form
     *
     * @ORM\Column(name="group_order", type="smallint")
     */
    private $order;

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
     * @return \FormBundle\Entity\Node\Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param \FormBundle\Entity\Node\Form $form
     * @return \FormBundle\Entity\Node\Group\Mapping
     */
    public function setForm(Form $form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return \FormBundle\Entity\Node\Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param \FormBundle\Entity\Node\Group $group
     * @return \FormBundle\Entity\Node\Group\Mapping
     */
    public function setGroup(Group $group)
    {
        $this->group = $group;
        return $this;
    }
}