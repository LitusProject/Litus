<?php

namespace CudiBundle\Form\Admin\Supplier;

/**
 * Edit Supplier
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CudiBundle\Form\Admin\Supplier\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'supplier_edit');
    }
}
