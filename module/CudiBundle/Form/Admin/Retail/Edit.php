<?php

namespace CudiBundle\Form\Admin\Retail;

/**
 * Edit Retail
 *
 */
class Edit extends \CudiBundle\Form\Admin\Retail\Add
{
    public function init()
    {
        parent::init();

        $this->remove('owner')
            ->add(
                array(
                    'type' => 'hidden',
                    'name' => 'owner',
                )
            );

        $this->remove('article')
            ->add(
                array(
                    'type' => 'hidden',
                    'name' => 'article',
                )
            );

        $this->remove('submit')
            ->addSubmit('Save', 'retail_edit');

        $this->bind($this->retail);
    }
}
