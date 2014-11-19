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

namespace FormBundle\Form\Admin\Field\Field;

use CommonBundle\Component\Form\FieldsetInterface,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    CommonBundle\Entity\General\Language;

/**
 * Add Dropdown Field
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Timeslot extends \CommonBundle\Component\Form\Admin\Fieldset\Tabbable
{
    public function init()
    {
        $this->add(array(
            'type'     => 'datetime',
            'name'     => 'start_date',
            'label'    => 'Start Date',
            'required' => $this->isRequired(),
        ));

        $this->add(array(
            'type'     => 'datetime',
            'name'     => 'end_date',
            'label'    => 'End Date',
            'required' => $this->isRequired(),
            'options'  => array(
                'input' => array(
                    'validators' => array(
                        new DateCompareValidator('start_date', 'd/m/Y H:i'),
                    ),
                ),
            ),
        ));

        parent::init();
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(array(
            'type'     => 'text',
            'name'     => 'location',
            'label'    => 'Location',
            'required' => $isDefault,
            'options'  => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $container->add(array(
            'type'     => 'text',
            'name'     => 'extra_info',
            'label'    => 'Extra Information',
            'required' => $isDefault,
            'options'  => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));
    }
}
