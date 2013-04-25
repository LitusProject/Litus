<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
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

namespace CudiBundle\Form\Admin\Article\File;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CudiBundle\Entity\Files\Mapping as FileMapping,
    Zend\Form\Element\Submit;

/**
 * Edit File
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    public function __construct(FileMapping $mapping, $options = null)
    {
        parent::__construct($options);

        $this->remove('file');
        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'file_edit');
        $this->add($field);

        $this->populateFromFile($mapping);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();

        $inputFilter->remove('file');

        return $inputFilter;
    }
}
