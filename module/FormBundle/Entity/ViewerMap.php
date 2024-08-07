<?php

namespace FormBundle\Entity;

use CommonBundle\Entity\User\Person;
use Doctrine\ORM\Mapping as ORM;
use FormBundle\Entity\Node\Form;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\ViewerMap")
 * @ORM\Table(name="form_viewers_map")
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
     * @var Form The form of the mapping
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Node\Form")
     * @ORM\JoinColumn(name="form", referencedColumnName="id")
     */
    private $form;

    /**
     * @var Person The person of the mapping
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\User\Person")
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
     * @param Form         $form
     * @param Person|null  $person
     * @param boolean|null $edit
     * @param boolean|null $mail
     */
    public function __construct(Form $form, Person $person = null, $edit = null, $mail = null)
    {
        $this->person = $person;
        $this->form = $form;
        $this->edit = $edit;
        $this->mail = $mail;
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
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param  Person $person
     * @return self
     */
    public function setPerson(Person $person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEdit()
    {
        return $this->edit;
    }

    /**
     * @param  boolean $edit
     * @return self
     */
    public function setEdit($edit)
    {
        $this->edit = $edit;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isMail()
    {
        return $this->mail;
    }

    /**
     * @param  boolean $mail
     * @return self
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }
}
