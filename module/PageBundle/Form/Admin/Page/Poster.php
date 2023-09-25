<?php

namespace PageBundle\Form\Admin\Page;

/**
 * Page poster form.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Poster extends \CommonBundle\Component\Form\Admin\Form
{
    const FILE_SIZE = '30MB';

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

        $this->addSubmit('Save', 'page_edit');
    }
}
