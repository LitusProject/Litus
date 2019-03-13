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

use ArrayAccess;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use Traversable;
use Zend\Form\ElementInterface as ZendElementInterface;
use Zend\Form\FieldsetInterface as ZendFieldsetInterface;
use Zend\InputFilter\Factory as InputFilterFactory;

/**
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Factory extends \Zend\Form\Factory implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Creates a form and sets data.
     *
     * $data can be:
     * - null: no data will be injected
     * - array: all values in the array will be injected in the form using
     *          set<Key>, with <Key> the array key of the value.
     * - object: the object will be bound to the object
     *
     * @param  array|\Traversable $spec
     * @param  array|object|null  $data
     * @return \Zend\Form\ElementInterface
     */
    public function create($spec, $data = null)
    {
        if (isset($spec['instance'])) {
            return $spec['instance'];
        }

        if ($data === null
            && is_array($spec)
            && isset($spec['options']['data'])
        ) {
            $data = $spec['options']['data'];
        }

        if ($data === null || is_array($data)) {
            $this->getFormElementManager()->setData($data);

            return parent::create($spec);
        } else {
            $form = parent::create($spec);
            $form->bind($data);

            return $form;
        }
    }

    /**
     * @return InputFilterFactory
     */
    public function getInputFilterFactory()
    {
        if ($this->inputFilterFactory === null) {
            $this->setInputFilterFactory(new InputFilterFactory());
        }

        return $this->inputFilterFactory;
    }

    /**
     * Configure an element based on the provided specification.
     *
     * @param  ZendElementInterface          $element
     * @param  array|Traversable|ArrayAccess $spec
     * @return ZendElementInterface
     */
    public function configureElement(ZendElementInterface $element, $spec)
    {
        $label = null;
        if (array_key_exists('label', $spec)) {
            $label = $spec['label'];
        }

        $value = null;
        if (array_key_exists('value', $spec)) {
            $value = $spec['value'];
        }

        $required = null;
        if (array_key_exists('required', $spec)) {
            $required = $spec['required'];
        }

        $spec = array_merge_recursive(
            array(
                'options' => array(
                    'label' => $label,
                ),
                'attributes' => array(
                    'value'    => $value,
                    'required' => $required,
                ),
            ),
            $spec
        );

        return parent::configureElement($element, $spec);
    }

    /**
     * Takes a list of element specifications, creates the elements, and injects them into the provided fieldset
     *
     * @param  array|Traversable|ArrayAccess $elements
     * @param  FieldsetInterface             $fieldset
     * @param  string                        $method   Method invoking this one (for exception messages)
     * @return void
     */
    protected function prepareAndInjectElements($elements, ZendFieldsetInterface $fieldset, $method)
    {
        if (is_array($elements)) {
            foreach ($elements as $key => $value) {
                if ($value instanceof ZendElementInterface) {
                    $elements[$key] = array(
                        'spec' => array(
                            'instance' => $value,
                        ),
                    );
                } elseif (is_array($value) && !isset($value['spec'])) {
                    $elements[$key] = array(
                        'spec' => $value,
                    );
                }
            }
        }

        parent::prepareAndInjectElements($elements, $fieldset, $method);
    }
}
