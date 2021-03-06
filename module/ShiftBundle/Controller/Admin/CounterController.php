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

namespace ShiftBundle\Controller\Admin;

use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Component\Util\File\TmpFile\Csv as CsvFile;
use CommonBundle\Entity\User\Person;
use DateTime;
use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;
use ShiftBundle\Component\Document\Generator\Counter\Csv as CsvGenerator;

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
        $academicYear = $this->getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $rewards_enabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.rewards_enabled');
        $points_enabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.points_enabled');

        return new ViewModel(
            array(
                'activeAcademicYear' => $academicYear,
                'academicYears'      => $academicYears,
                'rewards_enabled'   => $rewards_enabled,
                'points_enabled'   => $points_enabled,
            )
        );
    }

    public function unitsAction()
    {
        $academicYear = $this->getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $shifts = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findByAcademicYear($this->getAcademicYear());

        $units = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findAllActive();

        $unitsArray = array();
        foreach ($units as $unit) {
            $unitsArray[$unit->getId()] = $unit->getName();
        }

        $now = new DateTime();
        $result = array();
        foreach ($shifts as $shift) {
            if (!array_key_exists($shift->getUnit()->getId(), $unitsArray)) {
                continue;
            }

            if ($shift->getStartDate() > $now) {
                continue;
            }

            foreach ($shift->getResponsibles() as $responsible) {
                if (!isset($result[$shift->getUnit()->getId()][$responsible->getPerson()->getId()])) {
                    $result[$shift->getUnit()->getId()][$responsible->getPerson()->getId()] = array(
                        'name'  => $responsible->getPerson()->getFullName(),
                        'count' => 1,
                    );
                } else {
                    $result[$shift->getUnit()->getId()][$responsible->getPerson()->getId()]['count']++;
                }
            }

            foreach ($shift->getVolunteers() as $volunteer) {
                if (!isset($result[$shift->getUnit()->getId()][$volunteer->getPerson()->getId()])) {
                    $result[$shift->getUnit()->getId()][$volunteer->getPerson()->getId()] = array(
                        'name'  => $volunteer->getPerson()->getFullName(),
                        'count' => 1,
                    );
                } else {
                    $result[$shift->getUnit()->getId()][$volunteer->getPerson()->getId()]['count']++;
                }
            }
        }

        return new ViewModel(
            array(
                'activeAcademicYear' => $academicYear,
                'academicYears'      => $academicYears,
                'result'             => $result,
                'units'              => $unitsArray,
            )
        );
    }

    public function viewAction()
    {
        $person = $this->getPersonEntity();
        if ($person === null) {
            return new ViewModel();
        }

        $asResponsible = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllByPersonAsReponsible($person, $this->getAcademicYear());

        $asVolunteer = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllByPersonAsVolunteer($person, $this->getAcademicYear());

        $rewards_enabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.rewards_enabled');
        $points_enabled = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.points_enabled');

        $payed = array();
        foreach ($asVolunteer as $shift) {
            foreach ($shift->getVolunteers() as $volunteer) {
                if ($volunteer->getPerson() == $person) {
                    $payed[$shift->getId()] = $volunteer->isPayed();
                }
            }
        }

        return new ViewModel(
            array(
                'person'            => $person->getId(),
                'asResponsible'     => $asResponsible,
                'asVolunteer'       => $asVolunteer,
                'payed'             => $payed,
                'rewards_enabled'   => $rewards_enabled,
                'points_enabled'    => $points_enabled,
            )
        );
    }

    public function payedAction()
    {
        $this->initAjax();

        $shift = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findOneById($this->getParam('id'));

        if ($shift === null) {
            return new ViewModel();
        }

        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneById($this->getParam('person'));

        if ($person === null) {
            return new ViewModel();
        }

        foreach ($shift->getVolunteers() as $volunteer) {
            if ($volunteer->getPerson() == $person) {
                $volunteer->setPayed(
                    $this->getParam('payed') == 'true'
                );
            }
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
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

        if ($shift === null) {
            return new ViewModel();
        }

        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneById($this->getParam('person'));

        if ($person === null) {
            return new ViewModel();
        }

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
                    'status' => 'success',
                ),
            )
        );
    }

    public function searchAction()
    {
        $this->initAjax();

        $academicYear = $this->getAcademicYear();

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


            $asVolunteer = $this->getEntityManager()
                ->getRepository('ShiftBundle\Entity\Shift')
                ->findAllByPersonAsVolunteer($person, $academicYear);
            $unpayed = 0;
            $pointsCount = 0;
            foreach ($asVolunteer as $shift) {
                foreach ($shift->getVolunteers() as $volunteer) {
                    if ($volunteer->getPerson() == $person) {
                        if (!$volunteer->isPayed() && !$shift->getHandledOnEvent()) {
                            $unpayed += $shift->getReward();
                        }
                    }
                }
                $pointsCount += $shift->getPoints();
            }

            $item = (object) array();
            $item->id = $person->getId();
            $item->universityIdentification = $person->getUniversityIdentification();
            $item->name = $person->getFullName();
            $item->unpayed = $unpayed;
            $item->points = $pointsCount;
            $item->count = $shiftCount;
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    public function payoutAction()
    {
        $this->initAjax();

        $person = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\User\Person')
            ->findOneById($this->getParam('person'));

        if ($person === null) {
            return new ViewModel();
        }

        $shifts = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findAllByPersonAsVolunteer($person);

        foreach ($shifts as $shift) {
            foreach ($shift->getVolunteers() as $volunteer) {
                if ($volunteer->getPerson() == $person) {
                    $volunteer->setPayed(true);
                }
            }
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear|null
     */
    private function getAcademicYear()
    {
        $date = null;
        if ($this->getParam('academicyear') !== null) {
            $date = AcademicYear::getDateTime($this->getParam('academicyear'));
        }
        $academicYear = AcademicYear::getOrganizationYear($this->getEntityManager(), $date);

        if ($academicYear === null) {
            $this->flashMessenger()->error(
                'Error',
                'No academic year was found!'
            );

            $this->redirect()->toRoute(
                'shift_admin_shift_counter',
                array(
                    'action' => 'index',
                )
            );

            return;
        }

        return $academicYear;
    }

    /**
     * @return Person|null
     */
    private function getPersonEntity()
    {
        $person = $this->getEntityById('CommonBundle\Entity\User\Person');

        if (!($person instanceof Person)) {
            $this->flashMessenger()->error(
                'Error',
                'No person was found!'
            );

            $this->redirect()->toRoute(
                'shift_admin_shift_counter',
                array(
                    'action' => 'index',
                )
            );

            return;
        }

        return $person;
    }

    /**
     * @return array
     */
    public function exportAction()
    {
        $volunteers = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift\Volunteer')
            ->findAllNamesByAcademicYearQuery($this->getAcademicYear())->getResult();

        $file = new CsvFile();
        $document = new CsvGenerator($volunteers);
        $document->generateDocument($file);

        $filename = 'Volunteers.csv';

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Type'        => 'text/csv',
            )
        );
        $this->getResponse()->setHeaders($headers);

        return new ViewModel(
            array(
                'data' => $file->getContent(),
            )
        );
    }
}
