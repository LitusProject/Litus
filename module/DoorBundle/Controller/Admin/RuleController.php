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

namespace DoorBundle\Controller\Admin;

use DateInterval,
    DateTime,
    DoorBundle\Document\Rule,
    DoorBundle\Form\Admin\Rule\Add as AddForm,
    DoorBundle\Form\Admin\Rule\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * RuleController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class RuleController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromDocument(
            'DoorBundle\Document\Rule',
            $this->getParam('page')
        );

        $paginator = $this->getDocumentManager()
            ->getRepository('DoorBundle\Document\Rule')
            ->findAll();

        return new ViewModel(
            array(
                'logGraph' => $this->_getLogGraph(),
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'entityManager' => $this->getEntityManager(),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getDocumentManager());

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            $startDate = self::_loadDate($formData['start_date']);
            $endDate = self::_loadDate($formData['end_date']);

            if ($form->isValid() && $startDate && $endDate) {
                $formData = $form->getFormData($formData);

                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person\Academic');

                $academic = ('' == $formData['academic_id'])
                    ? $repository->findOneByUsername($formData['academic'])
                    : $repository->findOneById($formData['academic_id']);

                $rule = new Rule(
                    $startDate,
                    $endDate,
                    str_replace(':', '', $formData['start_time']),
                    str_replace(':', '', $formData['end_time']),
                    $academic
                );
                $this->getDocumentManager()->persist($rule);

                $this->getDocumentManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The rule was successfully created!'
                );

                $this->redirect()->toRoute(
                    'door_admin_rule',
                    array(
                        'action' => 'manage'
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        if (!($rule = $this->_getRule()))
            return new ViewModel();

        $form = new EditForm($this->getDocumentManager(), $rule);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            $startDate = self::_loadDate($formData['start_date']);
            $endDate = self::_loadDate($formData['end_date']);

            if ($form->isValid() && $startDate && $endDate) {
                $formData = $form->getFormData($formData);

                $repository = $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\User\Person');

                $person = ('' == $formData['person_id'])
                    ? $repository->findOneByUsername($formData['person'])
                    : $repository->findOneById($formData['person_id']);

                $rule->setAcademic($person)
                    ->setStartDate($startDate)
                    ->setEndDate($endDate)
                    ->setStartTime(str_replace(':', '', $formData['start_time']))
                    ->setEndTime(str_replace(':', '', $formData['end_time']));

                $this->getDocumentManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The rule was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'door_admin_rule',
                    array(
                        'action' => 'manage'
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromDocument(
            'DoorBundle\Document\Rule',
            $this->getParam('page')
        );

        $paginator = $this->getDocumentManager()
            ->getRepository('DoorBundle\Document\Rule')
            ->findOld();

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'entityManager' => $this->getEntityManager()
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($rule = $this->_getRule()))
            return new ViewModel();

        $this->getDocumentManager()->remove($rule);

        $this->getDocumentManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Rule
     */
    private function _getRule()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the rule!'
            );

            $this->redirect()->toRoute(
                'door_admin_rule',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $rule = $this->getDocumentManager()
            ->getRepository('DoorBundle\Document\Rule')
            ->findOneById($this->getParam('id'));

        if (null === $rule) {
            $this->flashMessenger()->error(
                'Error',
                'No door with the given ID was found!'
            );

            $this->redirect()->toRoute(
                'door_admin_rule',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $rule;
    }

    private function _getLogGraph()
    {
        if (null !== $this->getCache()) {
            if ($this->getCache()->hasItem('CommonBundle_Controller_RuleController_LogGraph')) {
                $now = new DateTime();
                if ($this->getCache()->getItem('CommonBundle_Controller_RuleController_LogGraph')['expirationTime'] > $now)
                    return $this->getCache()->getItem('CommonBundle_Controller_RuleController_LogGraph');
            }

            $this->getCache()->setItem(
                'CommonBundle_Controller_RuleController_LogGraph',
                $this->_getLogGraphData()
            );

            return $this->getCache()->getItem('CommonBundle_Controller_RuleController_LogGraph');
        }

        return $this->_getLogGraphData();
    }

    private function _getLogGraphData()
    {
        $now = new DateTime();

        $logGraphData = array(
            'expirationTime' => $now->add(new DateInterval('PT1H')),

            'labels' => array(),
            'dataset' => array()
        );

        $data = array();
        for ($i = 0; $i < 7; $i++) {
            $today = new DateTime('midnight');
            $labelDate = $today->sub(new DateInterval('P' . $i . 'D'));
            $data[$labelDate->format('d/m/Y')] = 0;
        }

        $today = new DateTime('midnight');
        $entries = $this->getDocumentManager()
            ->getRepository('DoorBundle\Document\Log')
            ->findAllSince($today->sub(new DateInterval('P6D')));

        foreach ($entries as $entry)
            $data[$entry->getTimestamp()->format('d/m/Y')]++;

        foreach (array_reverse($data) as $label => $value) {
            $logGraphData['labels'][] = $label;
            $logGraphData['dataset'][] = $value;
        }

        return $logGraphData;
    }

    /**
     * @param  string        $date
     * @return DateTime|null
     */
    private static function _loadDate($date)
    {
        return DateTime::createFromFormat('d#m#Y H#i', $date) ?: null;
    }
}
