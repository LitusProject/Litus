<?php

namespace CudiBundle\Form\Admin\Sale\Session;

/**
 * Close Sale Session
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Close extends \CudiBundle\Form\Admin\Sale\Session\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Close', 'sale_edit');

        $this->setData(
            $this->getHydrator()->extract($this->cashRegister)
        );
    }
}
