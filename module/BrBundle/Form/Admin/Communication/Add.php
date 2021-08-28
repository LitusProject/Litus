<?php

namespace BrBundle\Form\Admin\Communication;

use BrBundle\Entity\Communication;

/**
 * Add a communication.
 *
 * @author Stan Cardinaels <stan.cardinaels@vtk.be>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Communication';

    protected $communication;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type' => 'select',
                'name' => 'option',
                'label' => 'Communication Option',
                'required' => true,
                'attributes' => array(
                    'options' => unserialize(
                        $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\General\Config')
                            ->getConfigValue('br.communication_options')
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type' => 'select',
                'name' => 'companyId',
                'label' => 'Company',
                'required' => true,
                'attributes' => array(
                    'options' => $this->getCompanyArray(),
                ),
            )
        );

        $this->add(
            array(
                'type' => 'date',
                'nane' => 'date',
                'label' => 'Date',
                'required' => true,
            )
        );

        $this->add(
            array(
                'type' => 'text',
                'name' => 'audience',
                'label' => 'Audience',
                'required' => true,
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );


        $this->addSubmit('Add', 'communication_add');

        if ($this->communication !== null) {
            $this->bind($this->communication);
        }
    }

    public function setCommunication(Communication $communication)
    {
        $this->communication = $communication;

        return $this;
    }

    private function getCompanyArray()
    {
        $companies = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findAll();

        $companyArray = array(
            -1 => '',
        );
        foreach ($companies as $company) {
            $companyArray[$company->getId()] = $company->getName();
        }

        return $companyArray;
    }
}
