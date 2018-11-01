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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Form;

use Zend\InputFilter\InputFilterProviderInterface;
use Zend\InputFilter\InputProviderInterface;

/**
 * @method self setElementRequired(boolean $required)
 * @property array elements
 * @property array fieldsets
 */
trait FieldsetTrait
{
    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $spec = array(
            'type' => 'inputfilter',
        );

        $elements = array_merge($this->elements, $this->fieldsets);

        foreach ($elements as $name => $elementOrFieldset) {
            if ($elementOrFieldset instanceof InputFilterProviderInterface) {
                $spec[$name] = $elementOrFieldset->getInputFilterSpecification();
            } elseif ($elementOrFieldset instanceof InputProviderInterface) {
                $spec[$name] = $elementOrFieldset->getInputSpecification();
            }
        }

        return $spec;
    }

    /**
     * @param  boolean $required
     * @return self
     */
    public function setRequired($required = true)
    {
        foreach ($this->elements as $elementOrFieldset) {
            if ($elementOrFieldset instanceof ElementInterface) {
                $elementOrFieldset->setRequired($required);
            }
        }

        return $this->setElementRequired($required);
    }
}
