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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace FormBundle\Form\Admin\Form;

use FormBundle\Entity\Node\Form,
    FormBundle\Entity\Node\Form\Doodle;

/**
 * Edit Form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
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
            ->getRepository('FormBundle\Entity\Node\Group\Mapping')
            ->findOneByForm($this->form);

        if (null !== $group) {
            /** @var \CommonBundle\Component\Form\Admin\Element\DateTime $startDateField */
            $startDateField = $this->get('start_date');
            /** @var \CommonBundle\Component\Form\Admin\Element\DateTime $endDateField */
            $endDateField = $this->get('end_date');
            /** @var \CommonBundle\Component\Form\Admin\Element\Checkbox $activeField */
            $activeField = $this->get('active');
            /** @var \CommonBundle\Component\Form\Admin\Element\Text $maxField */
            $maxField = $this->get('max');
            /** @var \CommonBundle\Component\Form\Admin\Element\Checkbox $nonMemberField */
            $nonMemberField = $this->get('non_member');
            /** @var \CommonBundle\Component\Form\Admin\Element\Checkbox $editableByUser */
            $editableByUser = $this->get('editable_by_user');

            $startDateField->setAttribute('disabled', 'disabled')
                ->setRequired(false);
            $endDateField->setAttribute('disabled', 'disabled')
                ->setRequired(false);
            $activeField->setAttribute('disabled', 'disabled')
                ->setRequired(false);
            $maxField->setAttribute('disabled', 'disabled')
                ->setRequired(false);
            $nonMemberField->setAttribute('disabled', 'disabled')
                ->setRequired(false);
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

        if (null !== $this->form) {
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
