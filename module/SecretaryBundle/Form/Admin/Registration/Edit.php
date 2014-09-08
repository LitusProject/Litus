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

namespace SecretaryBundle\Form\Admin\Registration;

use SecretaryBundle\Component\Validator\CancelRegistration as CancelRegistrationValidator;
use SecretaryBundle\Entity\Registration;
use SecretaryBundle\Entity\Organization\MetaData;

/**
 * Edit Registration Data form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
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

        $this->remove('person_id');
        $this->remove('person');

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'cancel',
            'label'      => 'Cancelled',
            'value'      => $this->getRegistration()->isCancelled(),
            'options'    => array(
                'input' => array(
                    'validators' => array(
                        new CancelRegistrationValidator(),
                    ),
                ),
            ),
        ));

        $this->get('payed')
            ->setValue($this->getRegistration()->hasPayed());

        $metaData = $this->getMetaData();

        if (null !== $metaData) {
            $this->get('irreeel')
                ->setValue($metaData->receiveIrReeelAtCudi());
            $this->get('bakske')
                ->setValue($metaData->bakskeByMail());
            $this->get('tshirt_size')
                ->setValue($metaData->getTshirtSize());
        }

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
