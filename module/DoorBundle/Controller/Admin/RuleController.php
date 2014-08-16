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
        $form = $this->getForm('door_rule_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $rule = $form->hydrateObject();

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

        $form = $this->getForm('door_rule_edit', $rule);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
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
}
