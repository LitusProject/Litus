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

namespace FormBundle\Entity\Field;

use CommonBundle\Entity\General\Language,
    Doctrine\ORM\Mapping as ORM,
    FormBundle\Entity\Field,
    FormBundle\Entity\Node\Form;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Field\Checkbox")
 * @ORM\Table(name="forms.fields_checkboxes")
 */
class Checkbox extends Field
{
    /**
     * @param FormBundle\Entity\Node\Form $form
     * @param integer                     $order
     * @param bool                        $required
     * @param \FormBundle\Entity\Field    $visibityDecisionField
     * @param string                      $visibilityValue
     */
    public function __construct(Form $form, $order, $required, Field $visibityDecisionField = null, $visibilityValue = null)
    {
        parent::__construct($form, $order, $required, $visibityDecisionField, $visibilityValue);
    }

    /**
     * @param  \CommonBundle\Entity\General\Language $language
     * @param  boolean                               $value
     * @return string
     */
    public function getValueString(Language $language, $value)
    {
        return $value ? 'X' : '';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'checkbox';
    }
}
