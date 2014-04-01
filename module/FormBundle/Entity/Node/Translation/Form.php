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

namespace FormBundle\Entity\Node\Translation;

use CommonBundle\Entity\General\Language,
    Doctrine\ORM\Mapping as ORM,
    FormBundle\Entity\Node\Form as FormEntity;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Node\Translation\Form")
 * @ORM\Table(name="nodes.forms_translations")
 */
class Form
{
    /**
     * @var int The ID of this tanslation
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var FormEntity The form of this translation
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Node\Form", inversedBy="translations")
     * @ORM\JoinColumn(name="form", referencedColumnName="id")
     */
    private $form;

    /**
     * @var Language The language of this tanslation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;

    /**
     * @var string The title of this tanslation
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var string The introduction of this tanslation
     *
     * @ORM\Column(type="text")
     */
    private $introduction;

    /**
     * @var string The text on the submit button of this tanslation
     *
     * @ORM\Column(type="string")
     */
    private $submitText;

    /**
     * @var string The text on the update button of this tanslation
     *
     * @ORM\Column(type="string")
     */
    private $updateText;

    /**
     * @param FormEntity $form
     * @param Language   $language
     * @param string     $title
     * @param string     $introduction
     * @param string     $submitText
     * @param string     $updateText
     */
    public function __construct(FormEntity $form, Language $language, $title, $introduction, $submitText, $updateText)
    {
        $this->form = $form;
        $this->language = $language;
        $this->title = $title;
        $this->introduction = $introduction;
        $this->submitText = $submitText;
        $this->updateText = $updateText;
    }

    /**
     * @return FormEntity
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param  string $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }

    /**
     * @param  string $introduction
     * @return self
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubmitText()
    {
        return $this->submitText;
    }

    /**
     * @param  string $submitText
     * @return self
     */
    public function setSubmitText($submitText)
    {
        $this->submitText = $submitText;

        return $this;
    }

    /**
     * @return string
     */
    public function getUpdateText()
    {
        return $this->updateText;
    }

    /**
     * @param  string $updateText
     * @return self
     */
    public function setUpdateText($updateText)
    {
        $this->updateText = $updateText;

        return $this;
    }
}
