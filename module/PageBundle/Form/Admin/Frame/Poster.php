<?php

namespace PageBundle\Form\Admin\Frame;

/**
 * Frame poster form.
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class Poster extends \CommonBundle\Component\Form\Admin\Form
{
    const FILE_SIZE = '20MB';

    public function init()
    {
        parent::init();

        $this->setAttribute('id', 'uploadPoster');

        $this->add(
            array(
                'type'       => 'file',
                'name'       => 'poster',
                'label'      => 'Poster',
                'required'   => true,
                'attributes' => array(
                    'data-help' => 'The poster must be an image with a file size limit of ' . self::FILE_SIZE . '.',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name' => 'FileIsImage',
                            ),
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

        $this->addSubmit('Save', 'image_edit');
    }
}
