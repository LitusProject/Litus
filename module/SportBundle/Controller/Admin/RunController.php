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

namespace SportBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear,
    DateInterval,
    DateTime,
    SportBundle\Form\Admin\Runner\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * RunController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class RunController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function queueAction()
    {
        return new ViewModel(
            array(
                'socketUrl' => $this->getSocketUrl(),
                'authSession' => $this->getAuthentication()
                    ->getSessionObject(),
                'key' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('sport.queue_socket_key'),
            )
        );
    }

    public function lapsAction()
    {
        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('SportBundle\Entity\Lap')
                ->findAllPreviousLapsQuery($this->_getAcademicYear()),
            $this->getParam('page')
        );

        foreach ($paginator as $lap)
            $lap->setEntityManager($this->getEntityManager());

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'academicYear' => $this->_getAcademicYear(),
            )
        );
    }

    public function groupsAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'SportBundle\Entity\Group',
            $this->getParam('page'),
            array(
                'academicYear' => $this->_getAcademicYear()
            ),
            array(
                'name' => 'ASC'
            )
        );

        foreach ($paginator as $group)
            $group->setEntityManager($this->getEntityManager());

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'academicYear' => $this->_getAcademicYear(),
            )
        );
    }

    public function departmentsAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'SportBundle\Entity\Department',
            $this->getParam('page'),
            array(),
            array(
                'name' => 'ASC'
            )
        );

        foreach ($paginator as $department)
            $department->setEntityManager($this->getEntityManager());

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'academicYear' => $this->_getAcademicYear(),
            )
        );
    }

    public function pastaAction()
    {
        $runners = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Runner')
            ->findAll();

        $pastaRunners = array();
        foreach ($runners as $runner) {
            $runner->setEntityManager($this->getEntityManager());

            foreach ($runner->getLaps($this->_getAcademicYear()) as $lap) {
                if (
                    null !== $lap->getEndTime()
                    && $this->_convertDateIntervalToSeconds($lap->getLapTime()) <= 88
                ) {
                    if (isset($pastaRunners[$runner->getId()])) {
                        $pastaRunners[$runner->getId()]['count']++;
                    } else {
                        $pastaRunners[$runner->getId()] = array(
                            'name'  => $runner->getFullName(),
                            'count' => 1,
                        );
                    }
                }
            }
        }

        $paginator = $this->paginator()->createFromArray(
            $pastaRunners,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function identificationAction()
    {
        $runners = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Runner')
            ->findAllWithoutIdentification($this->_getAcademicYear());

        return new ViewModel(
            array(
                'runners' => $runners,
            )
        );
    }

    public function editAction()
    {
        if (!($runner = $this->_getRunner()))
            return new ViewModel();

        $form = new EditForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $runner->setRunnerIdentification($formData['runner_identification']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The runner was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'sport_admin_run',
                    array(
                        'action' => 'identification'
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'runner' => $runner,
                'form' => $form,
            )
        );
    }

    public function killSocketAction()
    {
        $this->initAjax();

        $baseDirectory = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
        system('kill $(ps aux | grep -i "php ' . $baseDirectory . '/bin/SportBundle/run.php --run" | grep -v grep | awk \'{print $2}\')');

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    public function updateAction()
    {
        return new ViewModel(
            array(
                'socketUrl' => $this->getSocketUrl(),
                'authSession' => $this->getAuthentication()
                    ->getSessionObject(),
                'key' => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('sport.queue_socket_key'),
            )
        );
    }

    public function _getRunner()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the runner!'
                )
            );

            $this->redirect()->toRoute(
                'sport_admin_run',
                array(
                    'action' => 'identification'
                )
            );

            return;
        }

        $runner = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Runner')
            ->findOneById($this->getParam('id'));

        if (null === $runner) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No runner with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'sport_admin_run',
                array(
                    'action' => 'identification'
                )
            );

            return;
        }

        return $runner;
    }

    private function _getAcademicYear()
    {
        if (null === $this->getParam('academicyear'))
            return $this->getCurrentAcademicYear();

        $start = AcademicYear::getDateTime($this->getParam('academicyear'));
        $start->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($start);

        if (null === $academicYear) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No academic year was found!'
                )
            );

            $this->redirect()->toRoute(
                'sport_admin_run',
                array(
                    'action' => 'queue'
                )
            );

            return;
        }

        return $academicYear;
    }

    /**
     * Returns the WebSocket URL.
     *
     * @return string
     */
    protected function getSocketUrl()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('sport.queue_socket_public');
    }

    private function _convertDateIntervalToSeconds(DateInterval $interval)
    {
        return $interval->h*3600 + $interval->i*60 + $interval->s;
    }
}
