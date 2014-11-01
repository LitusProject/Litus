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

namespace CudiBundle\Form\Admin\Sale\Booking;

use CommonBundle\Component\Validator\Typeahead\Person as PersonTypeaheadValidator;

/**
 * Booking by person
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Person extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'typeahead',
            'name'       => 'person',
            'label'      => 'Person',
            'required'   => true,
            'attributes' => array(
                'id'           => 'person',
                'style'        => 'width: 400px;',
            ),
            'options'    => array(
                'input' => array(
                    'validators'  => array(
                        new PersonTypeaheadValidator($this->getEntityManager()),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'submit',
            'name'       => 'submit',
            'value'      => 'Search',
            'attributes' => array(
                'class' => 'booking',
                'id'    => 'search',
            ),
        ));
    }
}
