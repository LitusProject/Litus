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

use Zend\Form\ElementInterface as OriginalElementInterface,
    Zend\Form\FieldsetInterface as OriginalFieldsetInterface,
    Zend\Form\FormInterface;

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
     * @param  array|\Traversable          $type
     * @param  array|null                  $data
     * @return \Zend\Form\ElementInterface
     */
    public function create($spec, array $data = null)
    {
        if (isset($spec['instance']))
            return $spec['instance'];

        if (null === $data && is_array($spec)
                && isset($spec['options']['data']))
            $data = $spec['options']['data'];

        $this->getFormElementManager()->setData($data);

        return parent::create($spec);
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
}
