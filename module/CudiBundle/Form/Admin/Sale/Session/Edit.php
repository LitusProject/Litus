<?php

namespace CudiBundle\Form\Admin\Sale\Session;

/**
 * Edit Sale Session content
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CudiBundle\Form\Admin\Sale\Session\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'sale_edit');

        $this->bind($this->cashRegister);
    }
}
