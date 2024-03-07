<?php

namespace BrBundle\Form\Admin\Match;

/**
 * Edit a match.
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class Edit extends Add
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
