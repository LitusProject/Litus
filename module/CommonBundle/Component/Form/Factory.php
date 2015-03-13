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

namespace CommonBundle\Component\Form;

use CommonBundle\Component\InputFilter\Factory as InputFilterFactory,
    Zend\Form\ElementInterface as OriginalElementInterface,
    Zend\Form\FieldsetInterface as OriginalFieldsetInterface;

/**
 * Creates forms
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @method FormElementManager getFormElementManager()
 */
class Factory extends \Zend\Form\Factory
{
    /**
     * @param FormElementManager $elementManager
     */
    public function __construct(FormElementManager $elementManager)
    {
        parent::__construct($elementManager);
    }

    /**
     * Creates a form and sets data.
     *
     * $data can be:
     * - null: no data will be injected
     * - array: all values in the array will be injected in the form using
     *          set<Key>, with <Key> the array key of the value.
     * - object: the object will be bound to the object
     *
     * @param  array|\Traversable          $spec
     * @param  array|object|null           $data
     * @return \Zend\Form\ElementInterface
     */
    public function create($spec, $data = null)
    {
        if (isset($spec['instance'])) {
            return $spec['instance'];
        }

        if (null === $data && is_array($spec)
                && isset($spec['options']['data'])) {
            $data = $spec['options']['data'];
        }

        if ($data === null || is_array($data)) {
            $this->getFormElementManager()->setData($data);

            return parent::create($spec);
        } else {
            // object, not null
            $form = parent::create($spec);
            $form->bind($data);

            return $form;
        }
    }

    public function configureElement(OriginalElementInterface $element, $spec)
    {
        parent::configureElement($element, $spec);

        if (($element instanceof ElementInterface)) {
            $element->setRequired(
                isset($spec['required'])
                ? (bool) $spec['required']
                : false
            );
        }

        if (isset($spec['label'])) {
            $element->setLabel($spec['label']);
        }

        if (isset($spec['value'])) {
            $element->setValue($spec['value']);
        }

        return $element;
    }

    protected function prepareAndInjectElements($elements, OriginalFieldsetInterface $fieldset, $method)
    {
        if (is_array($elements)) {
            foreach ($elements as $k => $v) {
                if ($v instanceof OriginalElementInterface) {
                    $elements[$k] = array(
                        'spec' => array(
                            'instance' => $v,
                        ),
                    );
                } elseif (is_array($v) && !isset($v['spec'])) {
                    $elements[$k] = array(
                        'spec' => $v,
                    );
                }
            }
        }

        return parent::prepareAndInjectElements($elements, $fieldset, $method);
    }

    public function getInputFilterFactory()
    {
        if (null === $this->inputFilterFactory) {
            $this->setInputFilterFactory(new InputFilterFactory());
        }

        return $this->inputFilterFactory;
    }
}
