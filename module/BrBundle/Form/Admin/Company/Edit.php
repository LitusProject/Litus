<?php

namespace BrBundle\Form\Admin\Company;

/**
 * Edit a company.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \BrBundle\Form\Admin\Company\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save', 'company_edit');
    }
}
