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

namespace BrBundle\Form\Admin\Company\Job;

use BrBundle\Entity\Company,
    BrBundle\Entity\Company\Job,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator;

/**
 * Add Job
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Company\Job';

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'text',
            'name'       => 'name',
            'label'      => 'Job Name',
            'required'   => true,
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => '100',
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'datetime',
            'name'       => 'start_date',
            'label'      => 'Start Date',
            'required'   => true,
        ));

        $this->add(array(
            'type'       => 'datetime',
            'name'       => 'end_date',
            'label'      => 'End Date',
            'required'   => true,
            'options'    => array(
                'input' => array(
                    'validators' => array(
                        new DateCompareValidator('start_date', 'd/m/Y H:i'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'sector',
            'label'      => 'Sector',
            'attributes' => array(
                'options' => $this->_getSectors(),
            ),
        ));

        $this->add(array(
            'type'       => 'textarea',
            'name'       => 'description',
            'label'      => 'Description',
            'required'   => true,
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'textarea',
            'name'       => 'benefits',
            'label'      => 'Benefits',
            'required'   => true,
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'textarea',
            'name'       => 'profile',
            'label'      => 'Profile',
            'required'   => true,
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'textarea',
            'name'       => 'contact',
            'label'      => 'Contact Information',
            'required'   => true,
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'city',
            'label'      => 'City',
            'required'   => true,
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'type',
            'label'      => 'Type',
            'attributes' => array(
                'options' => Job::$possibleTypes,
            ),
        ));

        $this->addSubmit('Add', 'company_add');
    }

    private function _getSectors()
    {
        $sectorArray = array();
        foreach (Company::$possibleSectors as $key => $sector) {
            $sectorArray[$key] = $sector;
        }

        return $sectorArray;
    }

    /*public function populateFromJob(Job $job)
    {
        $this->setData(
            array(
                'job_name' => $job->getName(),
                'description' => $job->getDescription(),
                'benefits' => $job->getBenefits(),
                'profile' => $job->getProfile(),
                'contact' => $job->getContact(),
                'city' => $job->getCity(),
                'start_date' => $job->getStartDate()->format('d/m/Y H:i'),
                'end_date' => $job->getEndDate()->format('d/m/Y H:i'),
                'sector' => $job->getSectorCode(),
            )
        );
    }*/
}
