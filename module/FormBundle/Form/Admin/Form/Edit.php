<?php

namespace FormBundle\Form\Admin\Form;

use FormBundle\Entity\Node\Form;
use FormBundle\Entity\Node\Form\Doodle;

/**
 * Edit Form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \FormBundle\Form\Admin\Form\Add
{
    /**
     * @var Form
     */
    private $form;

    public function init()
    {
        parent::init();

        $this->setAttribute('class', $this->getAttribute('class') . ' half_width');

        $group = $this->getEntityManager()
            ->getRepository('FormBundle\Entity\Node\Form\GroupMap')
            ->findOneByForm($this->form);

        if ($group !== null) {
            $startDateField = $this->get('start_date');
            $startDateField->setAttribute('disabled', 'disabled')
                ->setRequired(false);

            $endDateField = $this->get('end_date');
            $endDateField->setAttribute('disabled', 'disabled')
                ->setRequired(false);

            $activeField = $this->get('active');
            $activeField->setAttribute('disabled', 'disabled')
                ->setRequired(false);

            $maxField = $this->get('max');
            $maxField->setAttribute('disabled', 'disabled')
                ->setRequired(false);

            $nonMemberField = $this->get('non_member');
            $nonMemberField->setAttribute('disabled', 'disabled')
                ->setRequired(false);

            $editableByUser = $this->get('editable_by_user');
            $editableByUser->setAttribute('disabled', 'disabled')
                ->setRequired(false);
        }

        $this->remove('type');
        if ($this->form instanceof Doodle) {
            $this->remove('max');
        } else {
            $this->remove('names_visible_for_others');
            $this->remove('reminder_mail');
            $this->remove('reminder_mail_form');
        }

        $this->remove('submit')
            ->addSubmit('Save', 'form_edit');

        if ($this->form !== null) {
            $this->bind($this->form);
        }
    }

    public function setForm(Form $form)
    {
        $this->form = $form;

        return $this;
    }

    public function isDoodle()
    {
        return $this->form instanceof Doodle;
    }
}
