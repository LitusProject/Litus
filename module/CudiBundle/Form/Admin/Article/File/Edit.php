<?php

namespace CudiBundle\Form\Admin\Article\File;

/**
 * Edit File
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CudiBundle\Form\Admin\Article\File\Add
{
    public function init()
    {
        parent::init();

        $this->remove('file');

        $this->remove('submit')
            ->addSubmit('Save', 'file_edit');
    }
}
