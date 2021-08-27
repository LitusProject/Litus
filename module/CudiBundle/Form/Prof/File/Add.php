<?php

namespace CudiBundle\Form\Prof\File;

/**
 * Add File
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    const FILE_SIZE = '256MB';

    public function init()
    {
        parent::init();

        $this->setAttribute('id', 'uploadFile');

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'description',
                'label'    => 'Description',
                'required' => true,
                'options'  => array(
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
                    'size'      => 256,
                ),
                'options' => array(
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
    }
}
