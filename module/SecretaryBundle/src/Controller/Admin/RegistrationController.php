<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SecretaryBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear,
    DateInterval,
    DateTime,
    Zend\View\Model\ViewModel;

/**
 * RegistrationController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class RegistrationController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $academicYear = $this->_getAcademicYear();

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        $paginator = $this->paginator()->createFromEntity(
            'SecretaryBundle\Entity\Registration',
            $this->getParam('page'),
            array(
                'payed' => false,
                'academicYear' => $academicYear,
            ),
            array(
                'timestamp' => 'ASC'
            )
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
                'activeAcademicYear' => $academicYear,
                'academicYears' => $academicYears,
            )
        );
    }

    public function searchAction()
    {
        $academicYear = $this->_getAcademicYear();

        $this->initAjax();

        switch($this->getParam('field')) {
            case 'university_identification':
                $registrations = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Registration')
                    ->findAllByUniversityIdentification(
                        $this->getParam('string'),
                        $academicYear
                    );
                break;
            case 'name':
                $registrations = $this->getEntityManager()
                    ->getRepository('SecretaryBundle\Entity\Registration')
                    ->findAllByName(
                        $this->getParam('string'),
                        $academicYear
                    );
                break;
        }

        $numResults = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('search_max_results');

        array_splice($registrations, $numResults);

        $result = array();
        foreach($registrations as $registration) {
            $item = (object) array();
            $item->id = $registration->getId();
            $item->universityIdentification = $registration->getAcademic()->getUniversityIdentification();
            $item->name = $registration->getAcademic()->getFullName();
            $item->date = $registration->getTimestamp()->format('d/m/Y H:i');
            $item->payed = $registration->hasPayed();
            $result[] = $item;
        }

        return new ViewModel(
            array(
                'result' => $result,
            )
        );
    }

    /**
     * Returns the current academic year.
     *
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    private function _getAcademicYear()
    {
        if (null === $this->getParam('academicyear')) {
            $startAcademicYear = AcademicYear::getStartOfAcademicYear();

            $start = new DateTime(
                str_replace(
                    '{{ year }}',
                    $startAcademicYear->format('Y'),
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('start_organization_year')
                )
            );

            $next = clone $start;
            $next->add(new DateInterval('P1Y'));
            if ($next <= new DateTime())
                $start = $next;
        } else {
            $startAcademicYear = AcademicYear::getDateTime($this->getParam('academicyear'));

            $start = new DateTime(
                str_replace(
                    '{{ year }}',
                    $startAcademicYear->format('Y'),
                    $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\General\Config')
                        ->getConfigValue('start_organization_year')
                )
            );
        }
        $startAcademicYear->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByStart($start);

        if (null === $academicYear) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No academic year was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_study',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $academicYear;
    }
}
