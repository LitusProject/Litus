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

namespace PromBundle\Form\Admin\Bus;

/**
 * Add new buses
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'PromBundle\Hydrator\ReservationCode';

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'     => 'datetime',
            'name'     => 'departure_time',
            'label'    => 'Departure Time',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'validators' => array(
                        array(
                            'name' => 'date_compare',
                            'options' => array(
                                'first_date' => 'now',
                                'format' => 'd/m/Y H:i',
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'text',
            'name'     => 'nb_passengers',
            'label'    => 'Total passengers',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'int'),
                    ),
                ),
            ),
        ));

        //TODO add image for 'bus_add'
        $this->addSubmit('Add', 'bus_add');
    }
}
