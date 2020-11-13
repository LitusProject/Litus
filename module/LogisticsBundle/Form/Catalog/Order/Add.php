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

namespace LogisticsBundle\Form\Catalog\Order;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person\Academic;

/**
 * The form used to add a new Order.
 *
 * @author Robin Wroblowski
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = 'LogisticsBundle\Hydrator\Order';

    /**
     * @var Academic
     */
    protected $academic;

    /**
     * @var AcademicYear
     */
    protected $academicYear;

    public function init()
    {
        $this->add(
            array(
                'type'    => 'text',
                'name'    => 'order_name',
                'label'   => 'Order Name',
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        if ($this->academic->getOrganizationStatus($this->academicYear)) {
            $this->add(
                array(
                    'type' => 'select',
                    'name' => 'unit',
                    'attributes' => array(
                        'options' => $this->createUnitsArray(),
                    ),
                    'options' => array(
                        'input' => array(
                            'required' => true,
                        ),
                    ),
                )
            );
        }

        if (!$this->academic) {
            $this->add(
                array(
                    'type' => 'text',
                    'name' => 'contact',
                    'label' => 'Contact',
                    'required' => true,
                    'attributes' => array(
                        'id' => 'contact',
                        'autocomplete' => false,
                    ),
                    'options' => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                )
            );
        }
        else {
            $this->add(
                array(
                    'type' => 'hidden',
                    'name' => 'contact',
                    'label' => 'Contact',
                    'value' => $this->academic,
                )
            );
        }
        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'email',
                'label'      => 'Email',
                'required'   => true,
                'options' => array(
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
                'type'       => 'text',
                'name'       => 'location',
                'label'      => 'Location',
                'required'   => true,
                'options' => array(
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
                'type'     => 'datetime',
                'name'     => 'start_date',
                'label'    => 'Start Date',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'now',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'end_date',
                'label'    => 'End Date',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'start_date',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );


        $this->add(
            array(
                'type'       => 'textarea',
                'name'       => 'description',
                'label'      => 'Description',
                'attributes' => array(
                    'rows' => 3,
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Submit', 'btn btn-primary', 'submit');
    }

    private function createUnitsArray()
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
