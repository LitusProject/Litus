<?php

namespace CudiBundle\Form\Admin\Sale\Session\OpeningHour;

use CommonBundle\Component\Form\FieldsetInterface;
use CommonBundle\Entity\General\Language;

/**
 * Add opening hour
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    protected $hydrator = 'CudiBundle\Hydrator\Sale\Session\OpeningHour';

    protected function initBeforeTabs()
    {
        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'start_date',
                'label'    => 'Start',
                'required' => true,
            )
        );

        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'end_date',
                'label'    => 'End',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'start_date',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(
            array(
                'type'    => 'text',
                'name'    => 'comment',
                'label'   => 'Comment',
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
    }

    protected function initAfterTabs()
    {
        $this->addSubmit('Add', 'clock_add');
    }
}
