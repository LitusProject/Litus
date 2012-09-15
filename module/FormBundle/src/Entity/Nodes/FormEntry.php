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
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Nodes\FormEntry")
 * @ORM\Table(name="nodes.formentry")
 */
class FormEntry extends \CommonBundle\Entity\Nodes\Node
{

    /**
     * @var FormBundle\Entity\Nodes\FormSpecification The form this entry is part of.
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Nodes\FormSpecification")
     * @ORM\JoinColumn(name="form_id", referencedColumnName="id")
     */
    private $form;

    /**
     * @ORM\OneToMany(targetEntity="FormBundle\Entity\FormFieldEntry", mappedBy="formEntry")
     */
    private $fieldEntries;

    /**
     * @param \CommonBundle\Entity\Users\Person $person
     * @param \FormBundle\Entity\Nodes\FormSpecification $form
     */
    public function __construct($person, $form)
    {
        parent::__construct($person);

        $this->form = $form;
        $this->fieldEntries = new ArrayCollection();
    }

    /**
     * @return The form this field belongs to.
     */
    public function getForm() {
        return $this->form;
    }

    /**
     * @param FormBundle\Entity\FormFieldEntry The entry to add to this form.
     */
    public function addFieldEntry($fieldEntry) {
        $this->fieldEntries->add($fieldEntry);
        return $this;
    }

    public function getFieldEntries() {
        return $this->fieldEntries->toArray();
    }
}
