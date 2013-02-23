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

use CommonBundle\Entity\Users\Person,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\ViewerMap")
 * @ORM\Table(name="forms.viewers")
 */
class ViewerMap
{

    /**
     * @var integer The ID of the mapping
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CudiBundle\Entity\Article The form of the mapping
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Nodes\Form")
     * @ORM\JoinColumn(name="form", referencedColumnName="id")
     */
    private $form;

    /**
     * @var \SyllabusBundle\Entity\Subject The person of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Users\Person")
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var boolean The flag whether the person is allowed to edit the results too.
     *
     * @ORM\Column(type="boolean")
     */
    private $edit;

    /**
     * @var boolean The flag whether the person is allowed to mail the participants.
     *
     * @ORM\Column(type="boolean")
     */
    private $mail;

    /**
     * @param \FormBundle\Entity\Nodes\Form $form
     * @param \CommonBundle\Entity\Users\Person $person
     * @param boolean $edit
     * @param boolean $mail
     */
    public function __construct($form, $person, $edit, $mail)
    {
        $this->person = $person;
        $this->form = $form;
        $this->edit = $edit;
        $this->mail = $mail;
    }

    public function getId() {
        return $this->id;
    }

    /**
     * @return \FormBundle\Entity\Nodes\Form
     */
    public function getForm() {
        return $this->form;
    }

    /**
     * @return \CommonBundle\Entity\Users\Person
     */
    public function getPerson() {
        return $this->person;
    }

    /**
     * @return boolean
     */
    public function isEdit() {
        return $this->edit;
    }

    /**
     * @return boolean
     */
    public function isMail() {
        return $this->mail;
    }
}
