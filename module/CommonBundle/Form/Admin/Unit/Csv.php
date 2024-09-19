<?php

namespace CommonBundle\Form\Admin\Unit;

/**
 * The form used to add multiple members across different units via CSV.
 *
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class Csv extends \CommonBundle\Component\Form\Admin\Form
{
    const FILE_SIZE = '10MB';

    protected $hydrator = 'CommonBundle\Hydrator\General\Organization\Academic';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'file',
                'name'       => 'file',
                'label'      => 'Members csv',
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
                                    'extension' => 'csv',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'member_csv file_add');
    }
}
