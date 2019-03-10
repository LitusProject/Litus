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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace PromBundle\Form\Admin\ReservationCode;

use PromBundle\Entity\Bus\ReservationCode;

/**
 * Add Academic
 *
 * @author Matthias Swiggers <matthias.swiggers@studentit.be>
 */
class Academic extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'PromBundle\Hydrator\Bus\ReservationCode\Academic';

    /**
     * @var ReservationCode
     */
    private $code;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'typeahead',
                'name'     => 'person',
                'label'    => 'Name',
                'required' => true,
                'attributes' => array(
                    'autofocus' => 'true',
                ),
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'EntryAcademic'
                            ),
                            array('name' => 'TypeaheadPerson'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'select',
                'name'     => 'number_tickets',
                'label'    => 'Number of tickets',
                'required' => true,
                'attributes' => array(
                    'options' => $this->getNumberOptions(),
                ),
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'Int')
                        )
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'submit',
                'name'       => 'academic_add',
                'value'      => 'Add',
                'attributes' => array(
                    'class' => 'code_add',
                ),
            )
        );
    }

    private function getNumberOptions()
    {
        $nb = 10;
        $options = [];
        for($i = 0; $i <= $nb; $i++){
            $options[$i] = $i;
        }

        return $options;
    }
}
