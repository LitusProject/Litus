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

namespace BrBundle\Form\Admin\Event\Location;

use BrBundle\Entity\Event;
use BrBundle\Entity\Event\Location;

/**
 * Add a corporate relations event.
 *
 * @author Belian Callaerts <belian.callaerts@vtk.be>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Event\Location';

    /**
     * @var Event|null
     */
    protected $event;

    /**
     * @var Subscription |null
     */
    protected $location;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'number',
                'label'    => 'Number',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );
        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'x',
                'label'    => 'X position',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );
        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'y',
                'label'    => 'Y position',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'orientation',
                'label'      => 'Orientation',
                'required'   => true,
                'attributes' => array(
                    'id'      => 'orientation',
                    'options' => array(
                        'horizontal' => 'Horizontal',
                        'vertical'   => 'Vertical',
                    ),
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'type',
                'label'      => 'Type',
                'required'   => true,
                'attributes' => array(
                    'id'      => 'type',
                    'options' => $this->getTypes(),
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        // TODO: get companies working
        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'company',
                'label'      => 'Company',
                'required'   => false,
                'attributes' => array(
                    'id'      => 'company',
                    'options' => $this->getAttendingCompanies(),
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'add');

        if ($this->location !== null) {
            $this->bind($this->location);
        }
    }

    /**
     * @param  Event $event
     * @return self
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @param  Location $location
     * @return self
     */
    public function setLocation(Location $location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return array of possible location types
     */
    protected function getTypes()
    {
        return Event\Location::POSSIBLE_LOCATION_TYPES;
    }

    /**
     * @return array of possible location types
     */
    protected function getAttendingCompanies()
    {
        $companyMaps = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Event\CompanyMap')
            ->findAllByEventQuery($this->event)
            ->getResult();

        $companies = array('' => '');
        foreach ($companyMaps as $companyMap) {
            $companies[$companyMap->getCompany()->getId()] = $companyMap->getCompany()->getName();
        }

        return $companies;
    }
}
