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
    use ElementTrait;

    /**
     * Set a single element attribute.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return Element|ElementInterface
     */
    public function setAttribute($name, $value)
    {
        if ($name == 'required') {
            foreach ($this->getElements() as $elementOrFieldset) {
                if ($elementOrFieldset instanceof ElementInterface) {
                    if (!$elementOrFieldset->hasAttribute('required')) {
                        $elementOrFieldset->setAttributes(
                            array_merge(
                                $elementOrFieldset->getAttributes(),
                                array(
                                    'required' => $value,
                                )
                            )
                        );
                    }
                }
            }
        }

        return parent::setAttribute($name, $value);
    }

    /**
     * @return Input
     */
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
