<?php

namespace FormBundle\Form\Admin\Field;

/**
 * Edit Field
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \FormBundle\Form\Admin\Field\Add
{
    public function init()
    {
        parent::init();

        $typeField = $this->get('type');
        $typeField->setAttribute('disabled', 'disabled')
            ->setRequired(false);

        $visibilityFieldset = $this->get('visibility');
        $visibilityFieldset->get('value')->setAttribute('data-current_value', $this->field->getVisibilityValue());

        $this->remove('submit')
            ->remove('submit_repeat')
            ->addSubmit('Save', 'form_edit');

        $this->bind($this->field);
    }
}
