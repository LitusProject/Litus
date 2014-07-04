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

namespace ShiftBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear,
    DateTime,
    Zend\View\Model\ViewModel;

/**
 * CounterController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class CounterController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function indexAction()
    {
        $academicYear = $this->_getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'activeAcademicYear' => $academicYear,
                'academicYears' => $academicYears
            )
        );
    }

    public function unitsAction()
    {
        $academicYear = $this->_getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $shifts = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findByAcademicYear($this->_getAcademicYear());

        $units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAllActive();

        $unitsArray = array();
        foreach ($units as $unit)
            $unitsArray[$unit->getId()] = $unit->getName();

        $now = new DateTime();
        $result = array();
        foreach ($shifts as $shift) {
            if (!array_key_exists($shift->getUnit()->getId(), $unitsArray))
                continue;

            if ($shift->getStartDate() > $now)
                continue;

            foreach ($shift->getResponsibles() as $responsible) {
                if (!isset($result[$shift->getUnit()->getId()][$responsible->getPerson()->getId()])) {
                    $result[$shift->getUnit()->getId()][$responsible->getPerson()->getId()] = array(
                        'name' => $responsible->getPerson()->getFullName(),
                        'count' => 1
                    );
                } else {
                    $result[$shift->getUnit()->getId()][$responsible->getPerson()->getId()]['count']++;
                }
            }

            foreach ($shift->getVolunteers() as $volunteer) {
                if (!isset($result[$shift->getUnit()->getId()][$volunteer->getPerson()->getId()])) {
                    $result[$shift->getUnit()->getId()][$volunteer->getPerson()->getId()] = array(
                        'name' => $volunteer->getPerson()->getFullName(),
                        'count' => 1
                    );
                } else {
                    $result[$shift->getUnit()->getId()][$volunteer->getPerson()->getId()]['count']++;
                }
            }
        }

        return new ViewModel(
            array(
                'activeAcademicYear' => $academicYear,
                'academicYears' => $academicYears,
                'result' => $result,
                'units' => $unitsArray
            )
        );
    }

    public function viewAction()
    {
        if (!($person = $this->_getPerson()))
            return new ViewModel();

        $asResponsible = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllByPersonAsReponsible($person, $this->_getAcademicYear());

        $asVolunteer = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllByPersonAsVolunteer($person, $this->_getAcademicYear());

        $payed = array();
        foreach ($asVolunteer as $shift) {
            foreach ($shift->getVolunteers() as $volunteer) {
                if ($volunteer->getPerson() == $person)
                    $payed[$shift->getId()] = $volunteer->isPayed();
            }
        }

        return new ViewModel(
            array(
                'person' => $person->getId(),
                'asResponsible' => $asResponsible,
                'asVolunteer' => $asVolunteer,
                'payed' => $payed
            )
        );
    }

    public function payedAction()
    {
        $this->initAjax();

        $shift = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findOneById($this->getParam('id'));

        if (null === $shift)
            return new ViewModel();

        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneById($this->getParam('person'));

        if (null === $person)
            return new ViewModel();

        foreach ($shift->getVolunteers() as $volunteer) {
            if ($volunteer->getPerson() == $person) {
                $volunteer->setPayed(
                    'true' == $this->getParam('payed') ? true : false
                );
            }
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $shift = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findOneById($this->getParam('id'));

        if (null === $shift)
            return new ViewModel();

        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneById($this->getParam('person'));

        if (null === $person)
            return new ViewModel();

        foreach ($shift->getVolunteers() as $volunteer) {
            if ($volunteer->getPerson() == $person) {
                $shift->removePerson($person);

                $this->getEntityManager()->remove($volunteer);
                $this->getEntityManager()->flush();
            }
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $academicYear = $this->_getAcademicYear();

        $people = null;
        switch ($this->getParam('field')) {
            case 'university_identification':
                $people = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findAllByUniversityIdentification($this->getParam('string'));
                break;
            case 'name':
                $people = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic')
                    ->findAllByName($this->getParam('string'));
                break;
        }

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($people, $numResults);

        $result = array();
        foreach ($people as $person) {
            $shiftCount = $this->getEntityManager()
                ->getRepository('ShiftBundle\Entity\Shift')
                ->countAllByPerson($person, $academicYear);

            $item = (object) array();
            $item->id = $person->getId();
            $item->universityIdentification = $person->getUniversityIdentification();
            $item->name = $person->getFullName();
            $item->count = $shiftCount;
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    private function _getAcademicYear()
    {
        $date = null;
        if (null !== $this->getParam('academicyear'))
            $date = AcademicYear::getDateTime($this->getParam('academicyear'));
        $academicYear = AcademicYear::getOrganizationYear($this->getEntityManager(), $date);

        if (null === $academicYear) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No academic year was found!'
                )
            );

            $this->redirect()->toRoute(
                'shift_admin_shift_counter',
                array(
                    'action' => 'index'
                )
            );

            return;
        }

        return $academicYear;
    }

    private function _getPerson()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the person!'
                )
            );

            $this->redirect()->toRoute(
                'shift_admin_shift_counter',
                array(
                    'action' => 'index'
                )
            );

            return;
        }

        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneById($this->getParam('id'));

        if (null === $person) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No person with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'shift_admin_shift_counter',
                array(
                    'action' => 'index'
                )
            );

            return;
        }

        return $person;
    }
}
