<?php

namespace FormBundle\Entity\Field;

use CommonBundle\Entity\General\Language;
use Doctrine\ORM\Mapping as ORM;
use FormBundle\Entity\Field;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Field\Translation")
 * @ORM\Table(name="form_fields_translations")
 */
class Translation
{
    /**
     * @var integer The ID of this tanslation
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var Field The field of this translation
     *
     * @ORM\ManyToOne(targetEntity="FormBundle\Entity\Field", inversedBy="translations")
     * @ORM\JoinColumn(name="field", referencedColumnName="id")
     */
    private $field;

    /**
     * @var Language The language of this tanslation
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\Language")
     * @ORM\JoinColumn(name="language", referencedColumnName="id")
     */
    private $language;

    /**
     * @var string The label of this tanslation
     *
     * @ORM\Column(type="string")
     */
    private $label;

    /**
     * @param Field field
     * @param Language    $language
     * @param string      $label
     */
    public function __construct(Field $field, Language $language, $label)
    {
        $this->field = $field;
        $this->language = $language;
        $this->label = $label;
    }

    /**
     * @var int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Field
     */
    public function getField()
    {
        return $this->field;
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
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param  string $label
     * @return self
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }
}
