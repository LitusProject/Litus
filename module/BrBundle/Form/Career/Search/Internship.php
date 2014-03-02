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

namespace BrBundle\Form\Career\Search;

use BrBundle\Entity\Company,
    CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Submit,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Search for companies in a certain section
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class Internship extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var array The search possibilities
     */
    private static $possibleSearchTypes = array(
        'company' => 'Company',
        'internship' => 'Internship',
        'mostRecent' => 'Most Recent',
    );

    /**
     * @param null|string|int $name Optional name for the element
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->setAttribute('class', 'form-inline');
        $this->setAttributes('method', 'get');

        $field = new Select('searchType');
        $field->setAttribute('options', $this->_createSearchTypeArray());
        $this->add($field);

        $field = new Select('sector');
        $field->setAttribute('options', $this->_createSectorArray());
        $this->add($field);
    }

    private function _createSearchTypeArray()
    {
        return self::$possibleSearchTypes;
    }

    private function _createSectorArray()
    {
        $sectorArray = array('all' => 'All');
        foreach (Company::$possibleSectors as $key => $sector)
            $sectorArray[$key] = $sector;

        return $sectorArray;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'searchType',
                    'required' => true,
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'sector',
                    'required' => true,
                )
            )
        );

        return $inputFilter;
    }
}
