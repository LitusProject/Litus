<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace ShiftBundle\Form\Admin\Unit;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    Shiftbundle\Entity\Unit,
    Zend\Form\Element\Text,
    Zend\Form\Element\Submit;

/**
 * Edit Unit
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends Add
{
    /**
     * @param \ShiftBundle\Entity\Unit $unit The unit we're going to modify
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(Unit $unit, $name = null)
    {
        parent::__construct($name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'unit_edit');
        $this->add($field);

        $this->_populateFromUnit($unit);
    }

    private function _populateFromUnit(Unit $unit)
    {
        $data = array(
            'name' => $unit->getName()
        );

        $this->setData($data);
    }
}
