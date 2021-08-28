<?php

namespace DoorBundle\Controller\Admin;

use DateInterval;
use DateTime;
use DoorBundle\Entity\Rule;
use Laminas\View\Model\ViewModel;

/**
 * RuleController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class RuleController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('DoorBundle\Entity\Rule')
                ->findAll(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'logGraph'          => $this->getLogGraph(),
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
                'entityManager'     => $this->getEntityManager(),
            )
        );
    }

    public function addAction()
    {
        $form = $this->getForm('door_rule_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $rule = $form->hydrateObject();

                $this->getEntityManager()->persist($rule);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The rule was successfully created!'
                );

                $this->redirect()->toRoute(
                    'door_admin_rule',
                    array(
                        'action' => 'manage',
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
        $rule = $this->getRuleEntity();
        if ($rule === null) {
            return new ViewModel();
        }

        $form = $this->getForm('door_rule_edit', $rule);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Succes',
                    'The rule was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'door_admin_rule',
                    array(
                        'action' => 'manage',
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'academic' => $rule->getAcademic(),
                'form'     => $form,
            )
        );
    }

    public function oldAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('DoorBundle\Entity\Rule')
                ->findOld(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'     => $paginator,
                'entityManager' => $this->getEntityManager(),
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $rule = $this->getRuleEntity();
        if ($rule === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($rule);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Rule|null
     */
    private function getRuleEntity()
    {
        $rule = $this->getEntityManager()
            ->getRepository('DoorBundle\Entity\Rule')
            ->findOneBy(
                array(
                    'id' => $this->getParam('id'),
                )
            );

        if (!($rule instanceof Rule)) {
            $this->flashMessenger()->error(
                'Error',
                'No rule was found!'
            );

            $this->redirect()->toRoute(
                'door_admin_rule',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $rule;
    }

    /**
     * @return array
     */
    private function getLogGraph()
    {
        if ($this->getCache() !== null) {
            if ($this->getCache()->hasItem('CommonBundle_Controller_RuleController_LogGraph')) {
                $now = new DateTime();
                if ($this->getCache()->getItem('CommonBundle_Controller_RuleController_LogGraph')['expirationTime'] > $now) {
                    return $this->getCache()->getItem('CommonBundle_Controller_RuleController_LogGraph');
                }
            }

            $this->getCache()->setItem(
                'CommonBundle_Controller_RuleController_LogGraph',
                $this->getLogGraphData()
            );

            return $this->getCache()->getItem('CommonBundle_Controller_RuleController_LogGraph');
        }

        return $this->getLogGraphData();
    }

    /**
     * @return array
     */
    private function getLogGraphData()
    {
        $now = new DateTime();

        $logGraphData = array(
            'expirationTime' => $now->add(new DateInterval('PT1H')),

            'labels'  => array(),
            'dataset' => array(),
        );

        $data = array();
        for ($i = 0; $i < 7; $i++) {
            $today = new DateTime('midnight');
            $labelDate = $today->sub(new DateInterval('P' . $i . 'D'));
            $data[$labelDate->format('d/m/Y')] = 0;
        }

        $today = new DateTime('midnight');
        $entries = $this->getEntityManager()
            ->getRepository('DoorBundle\Entity\Log')
            ->findAllSince($today->sub(new DateInterval('P6D')));

        foreach ($entries as $entry) {
            $data[$entry->getTimestamp()->format('d/m/Y')]++;
        }

        foreach (array_reverse($data) as $label => $value) {
            $logGraphData['labels'][] = $label;
            $logGraphData['dataset'][] = $value;
        }

        return $logGraphData;
    }
}
