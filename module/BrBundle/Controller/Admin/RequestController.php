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

namespace BrBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

/**
 * RequestController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class RequestController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $vacancyRequests = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Request\RequestVacancy')
            ->findNewRequests();

        $internshipRequests = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Request\RequestInternship')
            ->findNewRequests();

        return new ViewModel(
            array(
                'vacancyRequests' => $vacancyRequests,
                'internshipRequests' => $internshipRequests,
            )
        );
    }

    public function viewAction()
    {
        if (!($request = $this->_getRequest()))
            return new ViewModel();

        switch ($request->getJob()->getType()) {
            case 'internship':
                $this->redirect()->toRoute(
                    'br_career_internship',
                    array(
                        'action' => 'view',
                        'id' => $request->getJob()->getId()
                    )
                );
                break;

            case 'vacancy':
                $this->redirect()->toRoute(
                    'br_career_vacancy',
                    array(
                        'action' => 'view',
                        'id' => $request->getJob()->getId()
                    )
                );
                break;

            default:break;
        }
    }

    public function viewEditAction()
    {
        if (!($request = $this->_getRequest()))
            return new ViewModel();

        switch ($request->getJob()->getType()) {
            case 'internship':
                $this->redirect()->toRoute(
                    'br_career_internship',
                    array(
                        'action' => 'view',
                        'id' => $request->getEditJob()->getId()
                    )
                );
                break;

            case 'vacancy':
                $this->redirect()->toRoute(
                    'br_career_vacancy',
                    array(
                        'action' => 'view',
                        'id' => $request->getEditJob()->getId()
                    )
                );
                break;

            default:break;
        }
    }

    public function approveAction()
    {
        if (!($request = $this->_getRequest()))
            return new ViewModel();

        $request->approveRequest();
        $request->handled();

        $this->getEntityManager()->persist($request);
        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::ERROR,
                'Error',
                'The request was succesfully approved.'
            )
        );

        $this->redirect()->toRoute(
            'br_admin_request',
            array(
                'action' => 'manage'
            )
        );

        return new ViewModel();
    }

    public function rejectAction()
    {
        if (!($request = $this->_getRequest()))
            return new ViewModel();

        $request->rejectRequest();
        $request->handled();

        $this->getEntityManager()->persist($request);
        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::ERROR,
                'Error',
                'The request was succesfully rejected.'
            )
        );

        $this->redirect()->toRoute(
            'br_admin_request',
            array(
                'action' => 'manage'
            )
        );

        return new ViewModel();
    }

    private function _getRequest()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the request!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_request',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $request = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Company\Request')
            ->findRequestById($this->getParam('id'));

        if (null === $request) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No request with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_request',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $request;
    }

    private function _getSectors()
    {
        $sectorArray = array();
        foreach (Company::$possibleSectors as $key => $sector)
            $sectorArray[$key] = $sector;

        return $sectorArray;
    }
}
