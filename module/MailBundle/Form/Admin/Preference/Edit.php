<?php

namespace MailBundle\Form\Admin\Preference;

/**
 * Edit Preference
 */
class Edit extends Add
{
    public function init()
    {
        parent::init();

        $this->remove('attribute')
            ->add(
                array(
                    'type' => 'hidden',
                    'name' => 'attribute',
                )
            );

        $this->remove('default_value')
            ->add(
                array(
                    'type' => 'hidden',
                    'name' => 'default_value',
                )
            );

        // TODO check how this works with API, what happens after editing default value?
        // TODO is there API support for renaming attributes?

        $this->remove('submit')
            ->addSubmit('Save', 'preference_edit');
    }
}
