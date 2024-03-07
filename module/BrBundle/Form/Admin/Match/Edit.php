<?php

namespace BrBundle\Form\Admin\Match;

/**
 * Edit an event.
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class Edit extends \BrBundle\Form\Admin\Match\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->add(
                array(
                    'type'       => 'submit',
                    'name'       => 'company_edit',
                    'value'      => 'Save',
                    'attributes' => array(
                        'class' => 'company_add',
                    ),
                )
            );
    }
}
