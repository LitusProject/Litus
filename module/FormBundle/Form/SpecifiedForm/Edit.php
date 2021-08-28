<?php

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
