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

namespace FormBundle\Form\SpecifiedForm;

use FormBundle\Entity\Field\File as FileFieldEntity;

/**
 * Specifield Form Edit
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit');
        $this->addSubmit($this->_form->getUpdateText($this->_language));
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        foreach ($this->_form->getFields() as $fieldSpecification) {
            if ($fieldSpecification instanceof FileFieldEntity) {
                $specs['field-' . $fieldSpecification->getId()]['required'] = false;
            }
        }

        return $specs;
    }
}
