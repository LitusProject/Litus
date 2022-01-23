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

namespace BrBundle\Form\Admin\Event\Company;


use BrBundle\Entity\Event\Subscription;
use BrBundle\Entity\Event\CompanyMap;

class Edit extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Event\CompanyMap';
    protected $companyMap;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'    => 'textarea',
                'name'    => 'notes',
                'label'   => 'Any notes for this company',
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
                'type'     => 'text',
                'name'     => 'attendees',
                'label'    => '# Attendees',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'int'),
                        ),
                    ),
                ),
            )
        );

        $masterFields = array();
        foreach ($this->getPossibleMasters() as $master_key => $master) {
            $masterFields[] = array(
                'type'       => 'select',
                'name'       => $master_key,
                'label'      => $master,
                'required'   => false,
                'attributes' => array(
                    'id'      => $master_key,
                    'options' => $this->getInterestOptions(),
                ),
                'options' => array(
                    'input' => array(
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            );
        }

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'master_interests',
                'label'    => 'Master interests',
                'elements' => $masterFields,
            )
        );

        $this->add(
            array(
                'type'     => 'checkbox',
                'name'     => 'information_checked',
                'label'    => 'The information has been checked and is correct',
            )
        );

        $this->remove('submit')
            ->addSubmit('Save', 'edit');

        if ($this->companyMap !== null) {
            $this->bind($this->companyMap);
        }
    }

    public function getInterestOptions() {
        return array(
            'not interested'    => 'Not interested',
            'interested'        => 'Interested',
        );
    }

    /**
     * @param  CompanyMap|null $companyMap
     * @return self
     */
    public function setCompanyMap(CompanyMap $companyMap)
    {
        $this->companyMap = $companyMap;

        return $this;
    }

    /**
     * @param  CompanyMap|null $companyMap
     * @return self
     */
    private function getPossibleMasters()
    {
        return Subscription::POSSIBLE_STUDIES;
    }
}
