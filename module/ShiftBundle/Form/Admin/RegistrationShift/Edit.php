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

namespace ShiftBundle\Form\Admin\RegistrationShift;

use ShiftBundle\Entity\RegistrationShift;

/**
 * Edit RegistrationShift
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \ShiftBundle\Form\Admin\RegistrationShift\Add
{
    /**
     * @var RegistrationShift|null The shift to edit
     */
    private $registrationShift;

    public function init()
    {
        parent::init();

        if (!$this->registrationShift->canEditDates()) {
            $this->remove('start_date')
                ->add(
                    array(
                        'type' => 'hidden',
                        'name' => 'start_date',
                    )
                );

            $this->remove('end_date')
                ->add(
                    array(
                        'type' => 'hidden',
                        'name' => 'end_date',
                    )
                );
            $this->remove('visible_date')
                ->add(
                    array(
                        'type' => 'hidden',
                        'name' => 'visible_date',
                    )
                );

            $this->remove('signout_date')
                ->add(
                    array(
                        'type' => 'hidden',
                        'name' => 'signout_date',
                    )
                );
        }

        $this->remove('submit')
            ->addSubmit('Save', 'registrationShift_edit');

        $this->bind($this->registrationShift);
    }

    /**
     * @param  RegistrationShift $registrationShift
     * @return self
     */
    public function setRegistrationShift(RegistrationShift $registrationShift)
    {
        $this->registrationShift = $registrationShift;

        return $this;
    }
}
