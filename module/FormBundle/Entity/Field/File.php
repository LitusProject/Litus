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

namespace FormBundle\Entity\Field;

use CommonBundle\Entity\General\Language,
    Doctrine\ORM\Mapping as ORM,
    FormBundle\Entity\Field,
    FormBundle\Entity\Node\Form;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Fields\File")
 * @ORM\Table(name="forms.fields_files")
 */
class File extends Field
{
    /**
     * @var int The maximum size of the file.
     *
     * @ORM\Column(name="max_size", type="integer")
     */
    private $maxSize;

    /**
     * @param FormBundle\Entity\Node\Form $form
     * @param integer $order
     * @param bool $required
     * @param \FormBundle\Entity\Field $visibityDecisionField
     * @param string $visibilityValue
     */
    public function __construct(Form $form, $order, $required, Field $visibityDecisionField = null, $visibilityValue = null, $maxSize)
    {
        parent::__construct($form, $order, $required, $visibityDecisionField, $visibilityValue);
        $this->setMaxSize($maxSize);
    }

    /**
     * Returns the maximum size of the file
     *
     * @return integer The maximum size of the file
     */
    public function getMaxSize()
    {
        return $this->maxSize;
    }

    /**
     * @param integer $maxSize The maximum size of the file
     * @return \FormBundle\Entity\Fields\File
     */
    public function setMaxSize($maxSize)
    {
        if (!is_numeric($maxSize))
            $maxSize = 4;
        $this->maxSize = $maxSize;
        return $this;
    }

    /**
     * @param \CommonBundle\Entity\General\Language $language
     * @param boolean $value
     * @return string
     */
    public function getValueString(Language $language, $value) {
        return strlen($value) ? 'X' : '';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'file';
    }
}
