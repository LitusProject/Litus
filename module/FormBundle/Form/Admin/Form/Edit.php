<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace FormBundle\Form\Admin\Form;

use Doctrine\ORM\EntityManager,
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
     * @var \FormBundle\Entity\Node\Group\Mapping
     */
    private $_group;

    /**
     * @param \Doctrine\ORM\EntityManager  $entityManager The EntityManager instance
     * @param \FormBundle\Entity\Node\Form $form          The notification we're going to modify
     * @param null|string|int              $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Form $form, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->_group = $entityManager->getRepository('FormBundle\Entity\Node\Group\Mapping')
            ->findOneByForm($form);

        if (null !== $this->_group) {
            $this->get('start_date')->setAttribute('disabled', 'disabled');
            $this->get('end_date')->setAttribute('disabled', 'disabled');
            $this->get('active')->setAttribute('disabled', 'disabled');
            $this->get('max')->setAttribute('disabled', 'disabled');
            $this->get('non_members')->setAttribute('disabled', 'disabled');
            $this->get('editable_by_user')->setAttribute('disabled', 'disabled');
        }

        $this->remove('type');
        if ($form instanceOf Doodle) {
            $this->remove('max');
        } else {
            $this->remove('names_visible_for_others');
        }

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
            'mail_from'        => $form->hasMail() ? $form->getMail()->getFrom() : '',
            'mail_bcc'         => $form->hasMail() ? $form->getMail()->getBcc() : '',
        );

        foreach ($this->getLanguages() as $language) {
            $data['title_' . $language->getAbbrev()] = $form->getTitle($language, false);
            $data['introduction_' . $language->getAbbrev()] = $form->getIntroduction($language, false);
            $data['submittext_' . $language->getAbbrev()] = $form->getSubmitText($language, false);
            $data['updatetext_' . $language->getAbbrev()] = $form->getUpdateText($language, false);
            $data['mail_subject_' . $language->getAbbrev()] = $form->hasMail() ? $form->getMail()->getSubject($language, false) : '';
            $data['mail_body_' . $language->getAbbrev()] = $form->hasMail() ? $form->getMail()->getContent($language, false) : '';
        }

        if ($form instanceOf Doodle) {
            $data['names_visible_for_others'] = $form->getNamesVisibleForOthers();
            $data['reminder_mail'] = $form->hasReminderMail();

            if ($form->hasReminderMail()) {
                $data['reminder_mail_from'] = $form->getReminderMail()->getFrom();
                $data['reminder_mail_bcc'] = $form->getReminderMail()->getBcc();
                foreach ($this->getLanguages() as $language) {
                    $data['reminder_mail_subject_' . $language->getAbbrev()] = $form->getReminderMail()->getSubject($language, false);
                    $data['reminder_mail_body_' . $language->getAbbrev()] = $form->getReminderMail()->getContent($language, false);
                }
            }
        }

        $this->setData($data);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();

        if (null !== $this->_group) {
            $inputFilter->remove('start_date');
            $inputFilter->remove('end_date');
            $inputFilter->remove('active');
            $inputFilter->remove('max');
            $inputFilter->remove('non_members');
            $inputFilter->remove('editable_by_user');
        }

        return $inputFilter;
    }
}
