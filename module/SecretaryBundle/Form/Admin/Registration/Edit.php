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

use Doctrine\ORM\EntityManager,
    SecretaryBundle\Entity\Registration,
    SecretaryBundle\Entity\Organization\MetaData;

/**
 * Edit Registration Data form
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @var Registration The registration data
     */
    protected $_registration = null;

    /**
     * @var MetaData The meta data
     */
    protected $_metaData = null;

    /**
     * @param EntityManager   $entityManager The EntityManager instance
     * @param Registration    $registration  The registration data
     * @param MetaData        $metaData      The meta data
     * @param null|string|int $name          Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Registration $registration, MetaData $metaData = null, $name = null)
    {
        parent::__construct($entityManager, $name);

        $this->_registration = $registration;
        $this->_metaData = $metaData;

        $this->remove('person_id');
        $this->remove('person');

        $this->get('payed')->setValue($registration->hasPayed());
        if ($metaData) {
            $this->get('irreeel')->setValue($metaData->receiveIrReeelAtCudi());
            $this->get('bakske')->setValue($metaData->bakskeByMail());
            $this->get('tshirt_size')->setValue($metaData->getTshirtSize());
        }

        $organization = $registration->getAcademic()->getOrganization($registration->getAcademicYear());
        $this->get('organization')->setValue($organization ? $organization->getId() : 0);
    }

    public function getInputFilter()
    {
        $inputFilter = parent::getInputFilter();

        $inputFilter->remove('person_id');
        $inputFilter->remove('person');

        return $inputFilter;
    }
}
