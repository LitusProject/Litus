<?php

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
        'company'    => 'Company',
        'internship' => 'Internship',
        'mostRecent' => 'Most Recent',
    );

    /**
     * @var Array of all possible sectors, locations and masters.
     */
    const POSSIBLE_SECTORS = array('all' => 'All') + Company::POSSIBLE_SECTORS;
    const POSSIBLE_LOCATIONS = array('all' => 'All') + Company::POSSIBLE_LOCATIONS;
    const POSSIBLE_MASTERS = array('all' => 'All') + Company::POSSIBLE_MASTERS;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'searchType',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->createSearchTypeArray(),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'sector',
                'required'   => true,
                'attributes' => array(
                    'options' => Internship::POSSIBLE_SECTORS,
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'location',
                'required'   => true,
                'attributes' => array(
                    'options' => Internship::POSSIBLE_LOCATIONS,
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'master',
                'required'   => true,
                'attributes' => array(
                    'options' => Internship::POSSIBLE_MASTERS,
                ),
            )
        );

        $this->addSubmit('Search');
        $this->get('submit')->setAttribute('class', 'btn btn-default');
    }

    private function createSearchTypeArray()
    {
        return self::$possibleSearchTypes;
    }
}
