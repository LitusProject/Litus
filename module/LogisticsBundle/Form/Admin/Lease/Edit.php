<?php

namespace LogisticsBundle\Form\Admin\Lease;

/**
 * Edits a lease
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class Edit extends \LogisticsBundle\Form\Admin\Lease\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Edit', 'lease_edit');
    }
}
