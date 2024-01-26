<?php

namespace OnBundle\Form\Admin\Slug;

/**
 * Edit Slug
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \OnBundle\Form\Admin\Slug\Add
{
    public function init()
    {
        parent::init();

        $nameField = $this->get('name');
        $nameField->setRequired();

        $this->remove('submit')
            ->addSubmit('Save', 'slug_edit');

        $dateTimeField = $this->get('expiration_date');

        if (!is_null($this->slug->getExpirationDate())) {
            $dateTimeField->setValue($this->slug->getExpirationDate()->format('d/m/Y'));
        } else {
            $dateTimeField->setValue('');
        }
    }
}
