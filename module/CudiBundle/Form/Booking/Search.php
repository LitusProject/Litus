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

namespace CudiBundle\Form\Booking;

use CommonBundle\Component\Form\Bootstrap\Element\Text,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Search articles
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Search extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $field = new Text('search_string');
        $field->setLabel('Search String')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xxlarge')
            ->setAttribute('pattern', '.{3}.*')
            ->setRequired();
        $this->add($field);
    }
}
