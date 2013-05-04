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

namespace CalendarBundle\Form\Admin\Event;

use CommonBundle\Component\Form\Admin\Element\File,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Event poster form.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Poster extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setAttribute('id', 'uploadPoster');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setAttribute('data-upload', 'progress');

        $field = new File('poster');
        $field->setLabel('Poster')
            ->setRequired();
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'image_edit');
        $this->add($field);
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'poster',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'fileextension',
                            'options' => array(
                                'extension' => 'jpg,png',
                            ),
                        ),
                        array(
                            'name' => 'filefilessize',
                            'options' => array(
                                'extension' => '2MB',
                            ),
                        ),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
