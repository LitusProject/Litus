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

namespace CudiBundle\Form\Admin\Article\File;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\File,
    CommonBundle\Component\Form\Admin\Element\Text,
    CudiBundle\Entity\File\Mapping as FileMapping,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add File
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->setAttribute('id', 'uploadFile');
        $this->setAttribute('enctype', 'multipart/form-data');

        $field = new Text('description');
        $field->setLabel('Description')
            ->setAttribute('size', 70)
            ->setRequired();
        $this->add($field);

        $field = new File('file');
        $field->setLabel('File')
            ->setAttribute('size', 70)
            ->setRequired();
        $this->add($field);

        $field = new Checkbox('printable');
        $field->setLabel('Printable');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'file_add');
        $this->add($field);
    }

    public function populateFromFile(FileMapping $mapping)
    {
        $this->setData(
            array(
                'description' => $mapping->getFile()->getDescription(),
                'printable' => $mapping->isPrintable()
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
                    'name'     => 'description',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'file',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'filefilessize',
                            'options' => array(
                                'max' => '256MB',
                            ),
                        ),
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
