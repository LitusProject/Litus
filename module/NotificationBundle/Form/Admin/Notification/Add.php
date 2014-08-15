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

namespace NotificationBundle\Form\Admin\Notification;

use CommonBundle\Component\Form\FieldsetInterface,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    CommonBundle\Entity\General\Language;

/**
 * Add Notification
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    protected $hydrator = 'NotificationBundle\Hydrator\Node\Notification';

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'datetime',
            'name'       => 'start_date',
            'label'      => 'Start Date',
            'required'   => true,
        ));

        $this->add(array(
            'type'       => 'datetime',
            'name'       => 'end_date',
            'label'      => 'End Date',
            'required'   => true,
        ));

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'active',
            'label'      => 'Active',
            'required'   => true,
        ));

        $this->addSubmit('Add', 'notification_add');
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(array(
            'type'       => 'textarea',
            'name'       => 'content',
            'label'      => 'Content',
            'required'   => $isDefault,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));
    }
}
