<?php

namespace CudiBundle\Form\Admin\Article\File;

/**
 * Add File
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'CudiBundle\Hydrator\File\Mapping';

    const FILE_SIZE = '256MB';

    public function init()
    {
        parent::init();

        $this->setAttribute('id', 'uploadFile');

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'description',
                'label'      => 'Description',
                'required'   => true,
                'attributes' => array(
                    'size' => 70,
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'file',
                'name'       => 'file',
                'label'      => 'File',
                'required'   => true,
                'attributes' => array(
                    'data-help' => 'The file can be of any type and has a file size limit of ' . self::FILE_SIZE . '.',
                    'size'      => 70,
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'FileSize',
                                'options' => array(
                                    'max' => self::FILE_SIZE,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'printable',
                'label'      => 'Printable',
                'attributes' => array(
                    'data-help' => 'Enabling this option will cause the file to be exported by exporting an order. This way these files will be also send to the supplier.',
                ),
            )
        );

        $this->addSubmit('Add', 'file_add');
    }
}
