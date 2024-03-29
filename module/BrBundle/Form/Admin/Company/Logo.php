<?php

namespace BrBundle\Form\Admin\Company;

/**
 * Company logo form.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Logo extends \CommonBundle\Component\Form\Admin\Form
{
    const FILE_SIZE = '2MB';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'file',
                'name'       => 'logo',
                'label'      => 'Logo',
                'required'   => true,
                'attributes' => array(
                    'data-help' => 'The logo must be an image with a file size limit of ' . self::FILE_SIZE . '.',
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
