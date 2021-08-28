<?php

namespace CudiBundle\Form\Retail;

/**
 * Edit Retail
 *
 */
class Edit extends \CudiBundle\Form\Retail\Add
{
    public function init()
    {
        parent::init();

        $this->get('article')->setAttribute('type', 'hidden');
        $this->add(
            array(
                'type'       => 'hidden',
                'name'       => 'retailId',
                'required'   => true,
            )
        );
        $this->remove('submit')
            ->addSubmit('Save', 'retail_edit');
    }
}
