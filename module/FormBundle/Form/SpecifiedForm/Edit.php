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

namespace FormBundle\Form\SpecifiedForm;

use FormBundle\Entity\Field\File as FileFieldEntity;

/**
 * Specifield Form Edit
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \FormBundle\Form\SpecifiedForm\Add
{
    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit($this->form->getUpdateText($this->language));
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        foreach ($this->form->getFields() as $fieldSpecification) {
            if ($fieldSpecification instanceof FileFieldEntity) {
                $specs['field-' . $fieldSpecification->getId()]['required'] = false;
            }
        }

        return $specs;
    }
}
