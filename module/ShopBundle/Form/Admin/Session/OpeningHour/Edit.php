<?php

namespace ShopBundle\Form\Admin\Session\OpeningHour;

/**
 * Edit Opening Hour
 *
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class Edit extends \ShopBundle\Form\Admin\Session\OpeningHour\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'clock_edit');
    }
}
