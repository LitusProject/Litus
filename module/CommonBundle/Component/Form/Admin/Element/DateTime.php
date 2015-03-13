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

namespace CommonBundle\Component\Form\Admin\Element;

/**
 * A date picker element
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class DateTime extends Text
{
    public function init()
    {
        parent::init();

        $this->setAttribute('placeholder', 'dd/mm/yyyy hh:mm')
            ->setAttribute('data-datepicker', true)
            ->setAttribute('data-timepicker', true);
    }

    public function getInputSpecification()
    {
        return array_merge_recursive(
            array(
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'date',
                        'options' => array(
                            'format' => 'd/m/Y H:i',
                        ),
                    ),
                ),
            ),
            parent::getInputSpecification()
        );
    }
}
