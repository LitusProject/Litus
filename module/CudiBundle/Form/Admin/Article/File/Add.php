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

/**
 * Add File
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'CudiBundle\Hydrator\File\Mapping';

    const FILESIZE = '256MB';

    public function init()
    {
        parent::init();

        $this->setAttribute('id', 'uploadFile')
            ->setAttribute('enctype', 'multipart/form-data');

        $this->add(array(
            'type'       => 'text',
            'name'       => 'description',
            'label'      => 'Description',
            'required'   => true,
            'attributes' => array(
                'size' => 70,
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'file',
            'name'       => 'file',
            'label'      => 'File',
            'required'   => true,
            'attributes' => array(
                'data-help' => 'The file can be of any type and has a filesize limit of ' . self::FILESIZE . '.',
                'size'      => 70,
            ),
            'options'    => array(
                'input' => array(
                    'validators' => array(
                        array(
                            'name' => 'filesize',
                            'options' => array(
                                'max' => self::FILESIZE,
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'printable',
            'label'      => 'Printable',
            'attributes' => array(
                'data-help' => 'Enabling this option will cause the file to be exported by exporting an order. This way these files will be also send to the supplier.',
            ),
        ));

        $this->addSubmit('Add', 'file_add');
    }
}
