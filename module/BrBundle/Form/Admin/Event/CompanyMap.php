<?php

namespace BrBundle\Form\Admin\Event;

use BrBundle\Entity\Event;

/**
 * Add a corporate relations event.
 *
 * @author Matthias Swiggers <matthias.swiggers@vtk.be>
 */
class CompanyMap extends \CommonBundle\Component\Form\Admin\Form
{

//    protected $hydrator = 'BrBundle\Hydrator\Event\CompanyMap';

    /**
     * @var Event
     */
    private $event;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'company',
                'label'      => 'Company',
                'required'   => true,
                'attributes' => array(
                    'id'      => 'company',
                    'options' => $this->getCompanyArray(),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'submit',
                'name'       => 'event_companyMap',
                'value'      => 'Add participant',
                'attributes' => array(
                    'class' => 'add',
                ),
            )
        );
    }

    /**
     * @return array
     */
    private function getCompanyArray()
    {
        $companies = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company')
            ->findAll();

        $companyArray = array(
            '' => '',
        );
        foreach ($companies as $company) {
            $companyArray[$company->getId()] = $company->getName();
        }

        return $companyArray;
    }

}
