<?php

namespace LogisticsBundle\Form\Admin\Driver;

/**
 * This form allows the user to edit the driver.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Edit extends \LogisticsBundle\Form\Admin\Driver\Add
{
    public function init()
    {
        parent::init();

        $this->remove('person');

        $this->remove('submit')
            ->addSubmit('Save', 'driver_edit');
    }
}
