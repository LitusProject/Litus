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

namespace FormBundle\Entity\Fields;

use CommonBundle\Entity\General\Language,
    CommonBundle\Entity\Users\Person,
    CommonBundle\Component\Util\Url,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM,
    FormBundle\Entity\Field;

/**
 * This entity stores the node item.
 *
 * @ORM\Entity(repositoryClass="FormBundle\Repository\Fields\Checkbox")
 * @ORM\Table(name="forms.fields_checkboxes")
 */
class Checkbox extends Field
{

    /**
     * @param FormBundle\Entity\Nodes\Form $form
     * @param integer $order
     * @param bool $required
     */
    public function __construct($form, $order, $required)
    {
        parent::__construct($form, $order, $required);
    }

    public function getValueString(Language $language, $value) {
        return $value ? 'X' : '';
    }

}
