<?php

namespace CommonBundle\Component\Form;

use CommonBundle\Component\InputFilter\Factory as InputFilterFactory;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareInterface;
use CommonBundle\Component\ServiceManager\ServiceLocatorAwareTrait;
use Laminas\Form\ElementInterface as LaminasElementInterface;
use Laminas\Form\FieldsetInterface as LaminasFieldsetInterface;

/**
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class Factory extends \Laminas\Form\Factory implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

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
     * @param  array|\Traversable $spec
     * @param  array|object|null  $data
     * @return \Laminas\Form\ElementInterface
     */
    public function create($spec, $data = null)
    {
        if (isset($spec['instance'])) {
            return $spec['instance'];
        }

        if ($data === null && is_array($spec)
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

    public function configureElement(LaminasElementInterface $element, $spec)
    {
        parent::configureElement($element, $spec);

        if ($element instanceof ElementInterface) {
            $element->setRequired(
                isset($spec['required']) ? (bool) $spec['required'] : false
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

    protected function prepareAndInjectElements($elements, LaminasFieldsetInterface $fieldset, $method)
    {
        if (is_array($elements)) {
            foreach ($elements as $k => $v) {
                if ($v instanceof LaminasElementInterface) {
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

        parent::prepareAndInjectElements($elements, $fieldset, $method);
    }

    public function getInputFilterFactory()
    {
        if ($this->inputFilterFactory === null) {
            $this->setInputFilterFactory(new InputFilterFactory());
        }

        return $this->inputFilterFactory;
    }
}
