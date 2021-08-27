<?php

namespace CudiBundle\Form\Admin\Sale\Article\Discount\Template;

/**
 * Edit Discount Template
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 */
class Edit extends \CudiBundle\Form\Admin\Sale\Article\Discount\Template\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'edit');
    }
}
