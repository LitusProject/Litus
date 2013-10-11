<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace FormBundle\Form\Admin\Form;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    Doctrine\ORM\EntityManager,
    FormBundle\Entity\Node\Form,
    FormBundle\Entity\Node\Form\Doodle,
    Zend\Form\Element\Submit;

/**
 * Edit Form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \FormBundle\Entity\Node\Form $form The notification we're going to modify
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Form $form, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->remove('type');
        if ($form instanceOf Doodle) {
            $this->remove('max');
        } else {
            $this->remove('names_visible_for_others');
        }

        $this->get('languages')
            ->setAttribute('class', $this->get('languages')->getAttribute('class') . ' half_width');

        $this->setAttribute('class', $this->getAttribute('class') . ' half_width');

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'form_edit');
        $this->add($field);

        $this->_populateFromForm($form);
    }

    private function _populateFromForm(Form $form)
    {
        $data = array(
            'start_date'       => $form->getStartDate()->format('d/m/Y H:i'),
            'end_date'         => $form->getEndDate()->format('d/m/Y H:i'),
            'active'           => $form->isActive(),
            'max'              => $form->getMax(),
            'multiple'         => $form->isMultiple(),
            'editable_by_user' => $form->isEditableByUser(),
            'non_members'      => $form->isNonMember(),
            'mail'             => $form->hasMail(),
            'mail_subject'     => $form->getMailSubject(),
            'mail_body'        => $form->getMailBody(),
            'mail_from'        => $form->getMailFrom(),
            'mail_bcc'         => $form->getMailBcc(),
        );

        foreach($this->getLanguages() as $language) {
            $data['title_' . $language->getAbbrev()] = $form->getTitle($language, false);
            $data['introduction_' . $language->getAbbrev()] = $form->getIntroduction($language, false);
            $data['submittext_' . $language->getAbbrev()] = $form->getSubmitText($language, false);
            $data['updatetext_' . $language->getAbbrev()] = $form->getUpdateText($language, false);
        }

        if ($form instanceOf Doodle) {
            $data['names_visible_for_others'] = $form->getNamesVisibleForOthers();
            $data['reminder_mail'] = $form->hasReminderMail();
            $data['reminder_mail_subject'] = $form->getReminderMailSubject();
            $data['reminder_mail_body'] = $form->getReminderMailBody();
            $data['reminder_mail_from'] = $form->getReminderMailFrom();
            $data['reminder_mail_bcc'] = $form->getReminderMailBcc();
        }

        $this->setData($data);
    }
}
