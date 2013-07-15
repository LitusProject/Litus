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
    CommonBundle\Entity\User\Person,
    CommonBundle\Component\Util\Url,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM,
    FormBundle\Entity\Field,
    FormBundle\Entity\Nodes\Form;

/**
 * An abstract class that stores a number of options.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Fields\Dropdown")
 * @ORM\Table(name="forms.fields_dropdowns")
 */
class Dropdown extends OptionSelector
{
    /**
     * @param FormBundle\Entity\Node\Form $form
     * @param integer $order
     * @param boolean $required
     * @param \FormBundle\Entity\Field $visibityDecisionField
     * @param string $visibilityValue
     */
    public function __construct(Form $form, $order, $required, Field $visibityDecisionField = null, $visibilityValue = null)
    {
        parent::__construct($form, $order, $required, $visibityDecisionField, $visibilityValue);
    }
}
