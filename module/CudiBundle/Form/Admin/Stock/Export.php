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

namespace CudiBundle\Form\Admin\Stock;

use Zend\Form\Element\Submit,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Export Stock
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Export extends SelectOptions
{
    /**
     * @param string $action
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($action = null, $name = null)
    {
        parent::__construct($name);

        $this->setAttribute('action', $action);

        $this->remove('select');

        $field = new Submit('export');
        $field->setValue('Export')
            ->setAttribute('id', 'export')
            ->setAttribute('class', 'download');
        $this->add($field);
    }
}