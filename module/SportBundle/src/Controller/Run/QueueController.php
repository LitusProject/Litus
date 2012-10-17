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

namespace SportBundle\Controller\Run;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear,
    DateInterval,
    DateTime,
    SportBundle\Entity\Lap,
    SportBundle\Entity\Runner,
    SportBundle\Form\Queue\Add as AddForm,
    Zend\View\Model\ViewModel;

/**
 * QueueController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class QueueController extends \SportBundle\Component\Controller\RunController
{
    public function signinAction()
    {
        $form = new AddForm();
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ('' != $formData['university_identification']
                    && !isset($formData['first_name'])
                    && !isset($formData['last_name'])
            ) {
                $academic = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\Users\People\Academic')
                    ->findOneByUniversityIdentification($formData['university_identification']);

                $formData['first_name'] = $academic->getFirstName();
                $formData['last_name'] = $academic->getLastName();
            }

            $form->setData($formData);

            if ($form->isValid()) {
                $runner = $this->getEntityManager()
                    ->getRepository('SportBundle\Entity\Runner')
                    ->findOneByUniversityIdentification($formData['university_identification']);

                if (null === $runner) {
                    $academic = $this->getEntityManager()
                        ->getRepository('CommonBundle\Entity\Users\People\Academic')
                        ->findOneByUniversityIdentification($formData['university_identification']);

                    $runner = new Runner(
                        $this->_getAcademicYear(),
                        $formData['first_name'],
                        $formData['last_name'],
                        null,
                        $academic
                    );
                }

                $lap = new Lap($this->_getAcademicYear(), $runner);
                $this->getEntityManager()->persist($lap);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'You have been succesfully added to the queue.'
                    )
                );

                $this->redirect()->toRoute(
                    'run_queue',
                    array(
                        'action' => 'signin'
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'form' => $form
            )
        );
    }

    public function getNameAction()
    {
        $this->initAjax();

        if (8 == strlen($this->getParam('university_identification'))) {
            $academic = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\Users\People\Academic')
                ->findOneByUniversityIdentification($this->getParam('university_identification'));

            if (null !== $academic) {
                return new ViewModel(
                    array(
                        'result' => (object) array(
                            'status' => 'success',
                            'firstName' => $academic->getFirstName(),
                            'lastName' => $academic->getLastName()
                        )
                    )
                );
            }
        }

        return new ViewModel();
    }

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
                'admin_shift_counter',
                array(
                    'action' => 'index'
                )
            );

            return;
        }

        return $academicYear;
    }
}
