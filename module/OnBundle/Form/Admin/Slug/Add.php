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

namespace OnBundle\Form\Admin\Slug;

use CommonBundle\Component\Form\Admin\Element\Text,
    Doctrine\ODM\MongoDB\DocumentManager,
    OnBundle\Component\Validator\Name as NameValidator,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Slug
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var DocumentManager The DocumentManager instance
     */
    protected $_documentManager = null;

    /**
     * @param DocumentManager $documentManager The DocumentManager instance
     * @param null|string|int $name            Optional name for the element
     */
    public function __construct(DocumentManager $documentManager, $name = null)
    {
        parent::__construct($name);

        $this->_documentManager = $documentManager;

        $field = new Text('name');
        $field->setLabel('Name');
        $this->add($field);

        $field = new Text('url');
        $field->setLabel('URL')
            ->setRequired();
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'slug_add');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'name',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new NameValidator($this->_documentManager),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'url',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'uri',
                        ),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
