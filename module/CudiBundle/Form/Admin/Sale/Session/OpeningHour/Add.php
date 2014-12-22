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

namespace CudiBundle\Form\Admin\Sale\Session\OpeningHour;



use CommonBundle\Component\Form\FieldsetInterface,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    CommonBundle\Entity\General\Language;

/**
 * Add opening hour
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form\Tabbable
{
    protected $hydrator = 'CudiBundle\Hydrator\Sale\Session\OpeningHour\OpeningHour';

    protected function initBeforeTabs()
    {
        $this->add(array(
            'type'     => 'datetime',
            'name'     => 'start',
            'label'    => 'Start',
            'required' => true,
        ));

        $this->add(array(
            'type'     => 'datetime',
            'name'     => 'end',
            'label'    => 'End',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'validators' => array(
                        new DateCompareValidator('start', 'd/m/Y H:i'),
                    ),
                ),
            ),
        ));
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(array(
            'type'    => 'text',
            'name'    => 'comment',
            'label'   => 'Comment',
            'options' => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));
    }

    protected function initAfterTabs()
    {
        $this->addSubmit('Add', 'clock_add');
    }
}
