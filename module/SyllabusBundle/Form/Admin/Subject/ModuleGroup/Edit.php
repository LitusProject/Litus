<?php

namespace SyllabusBundle\Form\Admin\Subject\ModuleGroup;

/**
 * Edit Subject
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \SyllabusBundle\Form\Admin\Subject\ModuleGroup\Add
{
    public function init()
    {
        parent::init();

        $this->remove('module_group');

        $this->remove('submit')
            ->addSubmit('Save', 'edit');

        $this->bind($this->mapping);
    }
}
