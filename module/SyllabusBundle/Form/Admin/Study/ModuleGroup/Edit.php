<?php

namespace SyllabusBundle\Form\Admin\Study\ModuleGroup;

/**
 * Edit Study
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \SyllabusBundle\Form\Admin\Study\ModuleGroup\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'edit');

        $this->bind($this->moduleGroup);
    }
}
