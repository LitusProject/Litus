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

namespace BrBundle\Form\Admin\Company;

use CommonBundle\Component\OldForm\Admin\Element\File,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Company logo form.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Logo extends \CommonBundle\Component\OldForm\Admin\Form
{
    const FILESIZE = '2MB';

    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setAttribute('enctype', 'multipart/form-data');

        $field = new File('logo');
        $field->setLabel('Logo')
            ->setAttribute('data-help', 'The logo must be an image of max ' . self::FILESIZE . '.')
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
                    'name'     => 'logo',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'fileisimage',
                        ),
                        array(
                            'name' => 'filefilessize',
                            'options' => array(
                                'max' => self::FILESIZE,
                            ),
                        ),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
