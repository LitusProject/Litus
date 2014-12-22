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

use BrBundle\Entity\Company;

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

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'select',
            'name'       => 'searchType',
            'required'   => true,
            'attributes' => array(
                'options' => $this->_createSearchTypeArray(),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'sector',
            'required'   => true,
            'attributes' => array(
                'options' => $this->_createSectorArray(),
            ),
        ));

        $this->addSubmit('Search');
        $this->get('submit')->setAttribute('class', 'btn btn-default');
    }

    private function _createSearchTypeArray()
    {
        return self::$possibleSearchTypes;
    }

    private function _createSectorArray()
    {
        $sectorArray = array('all' => 'All');
        foreach (Company::$possibleSectors as $key => $sector) {
            $sectorArray[$key] = $sector;
        }

        return $sectorArray;
    }
}
