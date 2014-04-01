<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
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
     * @var int The order of this form
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
