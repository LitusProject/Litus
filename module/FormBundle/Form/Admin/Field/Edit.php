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

namespace FormBundle\Form\Admin\Field;

/**
 * Edit Field
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    public function init()
    {
        parent::init();

        /** @var \CommonBundle\Component\Form\Admin\Element\Select $typeField */
        $typeField = $this->get('type');
        $typeField->setAttribute('disabled', 'disabled')
            ->setRequired(false);

        /** @var \CommonBundle\Component\Form\Fieldset $visibilityFieldset */
        $visibilityFieldset = $this->get('visibility');
        $visibilityFieldset->get('value')->setAttribute('data-current_value', $this->field->getVisibilityValue());

        $this->remove('submit');
        $this->remove('submit_repeat');

        $this->addSubmit('Save', 'form_edit');

        $this->bind($this->field);
    }
}
