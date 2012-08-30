<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Form\Bootstrap;

use Zend\Form\Element\Collection;
/**
 * Extending Zend's fieldset component, so that our forms look the way we want
 * them to.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
abstract class Fieldset extends \Zend\Form\Fieldset
{
    /**
     * Set a hash of element names/messages to use when validation fails
     *
     * @param  array|Traversable $messages
     * @return Element|ElementInterface|FieldsetInterface
     * @throws Exception\InvalidArgumentException
     */
    public function setMessages($messages)
    {
        parent::setMessages($messages);

        $fieldsets = $this->getFieldsets();
        foreach($fieldsets as $fieldset) {
            $fieldset->setMessages($messages);
        }

        return $this;
    }

    /**
     * Recursively populate values of attached elements and fieldsets
     *
     * @param  array|Traversable $data
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function populateValues($data)
    {
        parent::populateValues($data);

        $fieldsets = $this->getFieldsets();
        foreach($fieldsets as $fieldset) {
            if ($fieldset instanceof Collection)
                continue;

            $fieldset->populateValues($data);
        }

        return $this;
    }
}
