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

use Traversable;
use Zend\Form\ElementInterface;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\InputFilter\InputProviderInterface;

/**
 * FieldsetTrait
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
trait FieldsetTrait
{
    public function add($elementOrFieldset, array $flags = array())
    {
        if (is_array($elementOrFieldset)
            || ($elementOrFieldset instanceof Traversable && !($elementOrFieldset instanceof ElementInterface))
        ) {
            $label = null;
            if (array_key_exists('label', $elementOrFieldset)) {
                $label = $elementOrFieldset['label'];
            }

            $value = null;
            if (array_key_exists('value', $elementOrFieldset)) {
                $value = $elementOrFieldset['value'];
            }

            $required = null;
            if (array_key_exists('required', $elementOrFieldset)) {
                $required = $elementOrFieldset['required'];
            }

            $elementOrFieldset = array_merge_recursive(
                array(
                    'options' => array(
                        'label' => $label,
                    ),
                    'attributes' => array(
                        'value'    => $value,
                        'required' => $required,
                    ),
                ),
                $elementOrFieldset
            );
        }

        parent::add($elementOrFieldset, $flags);
    }

    public function setAttribute($name, $value)
    {
        if ($name == 'required') {
            foreach ($this->elements as $elementOrFieldset) {
                if ($elementOrFieldset instanceof ElementInterface) {
                    if (!$elementOrFieldset->hasAttribute('required')) {
                        $elementOrFieldset->setRequired($value);
                    }
                }
            }
        }

        return parent::setAttribute($name, $value);
    }

    public function getInputFilterSpecification()
    {
        $inputFilterSpecification = array();

        $elements = array_merge($this->elements, $this->fieldsets);
        foreach ($elements as $name => $elementOrFieldset) {
            if ($elementOrFieldset instanceof InputFilterProviderInterface) {
                $inputFilterSpecification[$name] = $elementOrFieldset->getInputFilterSpecification();
            } elseif ($elementOrFieldset instanceof InputProviderInterface) {
                $inputFilterSpecification[$name] = $elementOrFieldset->getInputSpecification();
            }
        }

        return $inputFilterSpecification;
    }
}
