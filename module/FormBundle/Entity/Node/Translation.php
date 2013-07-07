<?php

namespace FormBundle\Entity\Node;

use CommonBundle\Component\Util\Url,
    CommonBundle\Entity\General\Language,
    Doctrine\ORM\Mapping as ORM;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Node\Translation")
 * @ORM\Table(name="nodes.forms_translations")
 */
class Translation
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
     * @var \FormBundle\Entity\Node\Form The form of this translation
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Node\Form", inversedBy="translations")
     * @ORM\JoinColumn(name="form", referencedColumnName="id")
     */
    private $form;

    /**
     * @var \CommonBundle\Entity\General\Language The language of this tanslation
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
     * @param \FormBundle\Entity\Node\Form $form
     * @param \CommonBundle\Entity\General\Language $language
     * @param string $content
     * @param string $title
     */
    public function __construct(Form $form, Language $language, $title, $introduction, $submitText)
    {
        $this->form = $form;
        $this->language = $language;
        $this->title = $title;
        $this->introduction = $introduction;
        $this->submitText = $submitText;
    }

    /**
     * @return \FormBundle\Entity\Node\Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return \CommonBundle\Entity\General\Language
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
     * @param string $title
     *
     * @return \FormBundle\Entity\Node\Form
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
     * @param string $introduction
     *
     * @return \FormBundle\Entity\Node\Form
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
     * @param string $submitText
     *
     * @return \FormBundle\Entity\Node\Form
     */
    public function setSubmitText($submitText)
    {
        $this->submitText = $submitText;
        return $this;
    }
}
