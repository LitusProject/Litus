<?php

namespace ShiftBundle\Form\Shift\Search;

/**
 * Search Unit
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Unit extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function __construct($name = null)
    {
        parent::__construct($name, false, false);
    }

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'unit',
                'attributes' => array(
                    'options' => $this->createUnitsArray($academic = null),
                ),
                'options'    => array(
                    'input' => array(
                        'required' => true,
                    ),
                ),
            )
        );

        $this->remove('csrf');
    }

    protected function createUnitsArray($academic): array
    {
        $units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAllActiveAndDisplayed();

        $unitsArray = array(
            '' => '',
        );
        foreach ($units as $unit) {
            $unitsArray[$unit->getId()] = $unit->getName();
        }

        return $unitsArray;
    }
}
