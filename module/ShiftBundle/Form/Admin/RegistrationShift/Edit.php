<?php

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
//            $this->remove('visible_date')
//                ->add(
//                    array(
//                        'type' => 'hidden',
//                        'name' => 'visible_date',
//                    )
//                );
//
//            $this->remove('final_signin_date')
//                ->add(
//                    array(
//                        'type' => 'hidden',
//                        'name' => 'final_signin_date',
//                    )
//                );
//
//            $this->remove('signout_date')
//                ->add(
//                    array(
//                        'type' => 'hidden',
//                        'name' => 'signout_date',
//                    )
//                );
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
