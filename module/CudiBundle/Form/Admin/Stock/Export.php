<?php

namespace CudiBundle\Form\Admin\Stock;

/**
 * Export Stock
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Export extends \CudiBundle\Form\Admin\Stock\SelectOptions
{
    public function init()
    {
        parent::init();

        $this->remove('select')
            ->addSubmit('Export', 'download', 'export', array('id' => 'export'));
    }
}
