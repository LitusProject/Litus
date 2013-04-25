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

namespace CudiBundle\Form\Admin\Supplier;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CudiBundle\Entity\Supplier,
    Zend\Form\Element\Submit;

/**
 * Edit Supplier
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CudiBundle\Form\Admin\Supplier\Add
{
    /**
     * @param \CudiBundle\Entity\Supplier $supplier
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(Supplier $supplier, $name = null)
    {
        parent::__construct($name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'supplier_edit');
        $this->add($field);

        $this->populateFromSupplier($supplier);
    }
}
