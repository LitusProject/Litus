<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CommonBundle\Form\Admin\Config;

use CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Textarea,
    CommonBundle\Entity\General\Config,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit Configuration
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param \CommonBundle\Entity\Public\Config $entry The configuration entry we are editing
     * @param mixed $opts The form's options
     */
    public function __construct(Config $entry, $opts = null)
    {
        parent::__construct($opts);

        $field = new Text('key');
        $field->setLabel('Key')
            ->setAttribute('disabled', 'disabled');
        $this->add($field);

        if (strlen($entry->getValue()) > 40) {
            $field = new Textarea('value');
            $field->setLabel('Value')
                ->setRequired();
            $this->add($field);
        } else {
            $field = new Text('value');
            $field->setLabel('Value')
                ->setRequired();
            $this->add($field);
        }

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'config_edit');
        $this->add($field);

        $this->setData(
            array(
                'key' => $entry->getKey(),
                'value' => $entry->getValue()
            )
        );
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'value',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
