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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
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
class Vacancy extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var array The search posibilities
     */
    private static $possibleSearchTypes = array(
        'mostRecent' => 'Most Recent',
        'company' => 'Company',
        'vacancy' => 'Vacancy',
    );

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'select',
            'name'       => 'searchType',
            'required'   => true,
            'attributes' => array(
                'options' => $this->createSearchTypeArray(),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'sector',
            'required'   => true,
            'attributes' => array(
                'options' => $this->createSectorArray(),
            ),
        ));

        $this->addSubmit('Search');
        $this->get('submit')->setAttribute('class', 'btn btn-default');
    }

    private function createSearchTypeArray()
    {
        return self::$possibleSearchTypes;
    }

    private function createSectorArray()
    {
        $sectorArray = array('all' => 'All');
        foreach (Company::$possibleSectors as $key => $sector) {
            $sectorArray[$key] = $sector;
        }

        return $sectorArray;
    }
}
