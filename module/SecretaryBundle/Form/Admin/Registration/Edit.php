<?php

namespace SecretaryBundle\Form\Admin\Registration;

use SecretaryBundle\Entity\Organization\MetaData;
use SecretaryBundle\Entity\Registration;

/**
 * Edit Registration Data form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \SecretaryBundle\Form\Admin\Registration\Add
{
    /**
     * @var Registration
     */
    private $registration;

    /**
     * @var MetaData
     */
    private $metaData;

    public function init()
    {
        parent::init();

        $this->remove('person');

        $this->add(
            array(
                'type'    => 'checkbox',
                'name'    => 'cancel',
                'label'   => 'Cancelled',
                'value'   => $this->getRegistration()->isCancelled(),
                'options' => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'CancelRegistration'),
                        ),
                    ),
                ),
            )
        );

        $this->get('payed')->setValue($this->getRegistration()->hasPayed());

        $organization = $this->getRegistration()->getAcademic()->getOrganization($this->getRegistration()->getAcademicYear());
        $this->get('organization')->setValue($organization ? $organization->getId() : 0);
    }

    /**
     * @param Registration
     * @return self
     */
    public function setRegistration(Registration $registration)
    {
        $this->registration = $registration;

        return $this;
    }

    /**
     * @return Registration
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * @param MetaData
     * @return self
     */
    public function setMetaData(MetaData $metaData)
    {
        $this->metaData = $metaData;

        return $this;
    }

    /**
     * @return MetaData
     */
    public function getMetaData()
    {
        return $this->metaData;
    }
}
