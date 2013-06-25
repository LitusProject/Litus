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

namespace CudiBundle\Form\Admin\Prof\File;

use CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Form\Admin\Element\Text,
    CudiBundle\Entity\Files\Mapping as FileMapping,
    Zend\Form\Element\Submit;

/**
 * Confirm File add action
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Confirm extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @param \CudiBundle\Entity\Files\Mapping $mapping
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(FileMapping $mapping, $name = null)
    {
        parent::__construct($name);

        $this->setAttribute('id', 'uploadFile');

        $field = new Text('description');
        $field->setLabel('Description')
            ->setAttribute('size', 70)
            ->setRequired();
        $this->add($field);

        $field = new Checkbox('printable');
        $field->setLabel('Printable');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Confirm')
            ->setAttribute('class', 'file_add');
        $this->add($field);

        $this->populateFromFile($mapping);
    }

    public function populateFromFile(FileMapping $mapping)
    {
        $this->setData(
            array(
                'description' => $mapping->getFile()->getDescription(),
            )
        );
    }
}
