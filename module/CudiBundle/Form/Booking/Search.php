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

namespace CudiBundle\Form\Booking;

use CommonBundle\Component\Form\Bootstrap\Element\Text;

/**
 * Search articles
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Search extends \CommonBundle\Component\OldForm\Bootstrap\Form
{
    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->setAttribute('class', 'form-horizontal pull-right col-md-10');

        $field = new Text('search_string');
        $field->setLabel('Search String')
            ->setAttribute('pattern', '.{3}.*')
            ->setRequired();
        $this->add($field);
    }
}
