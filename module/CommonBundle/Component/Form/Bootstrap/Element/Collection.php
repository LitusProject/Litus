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

namespace CommonBundle\Component\Form\Bootstrap\Element;

use Zend\Form\FormInterface;

/**
 * Collection form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Collection extends \Zend\Form\Element\Collection
{
    /**
     * @return boolean
     */
    public function isCollection()
    {
        return true;
    }

    /**
     * Ensures state is ready for use. Here, we append the name of the fieldsets to every elements in order to avoid
     * name clashes if the same fieldset is used multiple times
     *
     * @param  Form $form
     * @return mixed|void
     */
    public function prepareElement(FormInterface $form)
    {
        foreach ($this->byName as $elementOrFieldset) {
            // Recursively prepare elements
            if ($elementOrFieldset instanceof ElementPrepareAwareInterface) {
                $elementOrFieldset->prepareElement($form);
            }
        }
    }

    /**
     * Populate values
     *
     * @param array|\Traversable $data
     * @throws \Zend\Form\Exception\InvalidArgumentException
     * @throws \Zend\Form\Exception\DomainException
     * @return void
     */
    public function populateValues($data)
    {
        foreach($data as $key => $value) {
            if (!$this->get($key))
                unset($data[$key]);
        }
        foreach ($this->byName as $name => $element) {
            if (!isset($data[$name]))
                $data[$name] = '';
        }
        parent::populateValues($data);
    }
}
