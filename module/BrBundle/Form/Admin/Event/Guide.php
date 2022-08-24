<?php

namespace BrBundle\Form\Admin\Event;

class Guide extends \CommonBundle\Component\Form\Admin\Form
{
    const FILE_SIZE = '200MB';

    protected $hydrator = 'BrBundle\Hydrator\Event';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'file',
                'name'       => 'file',
                'label'      => 'Company Guide',
                'attributes' => array(
                    'data-help' => 'The maximum file size is ' . self::FILE_SIZE . '.',
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
                            array(
                                'name'    => 'FileExtension',
                                'options' => array(
                                    'extension' => 'pdf',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'pdf_add');
    }
}
