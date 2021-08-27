<?php

namespace CudiBundle\Form\Admin\Sale\Financial;

/**
 * Search financial for period
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Period extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'date',
                'name'     => 'start_date',
                'label'    => 'Start Date',
                'required' => true,
            )
        );

        $this->add(
            array(
                'type'     => 'date',
                'name'     => 'end_date',
                'label'    => 'End Date',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'start_date',
                                    'format'     => 'd/m/Y',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Search', 'financial');
    }
}
